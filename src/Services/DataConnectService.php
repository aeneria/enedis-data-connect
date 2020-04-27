<?php

namespace Aeneria\EnedisDataConnectApi\Services;

use Aeneria\EnedisDataConnectApi\Exception\DataConnectConsentException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectDataNotFoundException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectQuotaExceededException;
use Aeneria\EnedisDataConnectApi\MeteringData;
use Aeneria\EnedisDataConnectApi\Token;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Implements DataConnect API from datahub-enedis
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/authorize-v1/
 */
class DataConnectService
{
    const GRANT_TYPE_CODE = 'authorization_code';
    const GRANT_TYPE_TOKEN = 'refresh_token';

    private $authEndpoint;
    private $meteringDataEndpoint;

    private $clientId;
    private $clientSecret;

    public function __construct(string $authEndpoint, string $meteringDataEndpoint, string $clientId, string $clientSecret)
    {
        $this->authEndpoint = $authEndpoint;
        $this->meteringDataEndpoint = $meteringDataEndpoint;

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get a URL to DataConnect consent page.
     *
     * @var string Durée du consentement demandé par l’application,
     * au format ISO 8601. Cette durée sera affichée au consommateur et ne peut
     * excéder 3 ans.
     *
     * @var string Paramètre de sécurité permettant de maintenir l’état
     * entre la requête et la redirection.
     */
    public function getConsentPageUrl(string $duration, string $state): string
    {
        return \sprintf(
            '%s/dataconnect/v1/oauth2/authorize?client_id=%s&response_type=code&state=%s&duration=%s',
            $this->authEndpoint,
            $this->clientId,
            $state,
            $duration
        );
    }

    /**
     * Get DataConnectToken from a grant code.
     */
    public function requestTokenFromCode(string $code): Token
    {
        return $this->requestToken(self::GRANT_TYPE_CODE, $code);
    }

    /**
     * Get DataConnectToken from a refreshToken.
     */
    public function requestTokenFromRefreshToken(string $refreshToken): Token
    {
        return $this->requestToken(self::GRANT_TYPE_TOKEN, $refreshToken);
    }

    private function requestToken(string $grantType, string $codeOrToken): Token
    {
        $body = [
            'grant_type' => $grantType,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => "http://app.aeneria.com",
        ];

        switch ($grantType) {
            case self::GRANT_TYPE_CODE:
                $body['code'] = $codeOrToken;
                break;
            case self::GRANT_TYPE_TOKEN:
                $body['refresh_token'] = $codeOrToken;
                break;
            default:
                throw new \InvalidArgumentException(\sprintf(
                    'Only "%s" or "%s" grant types are supported',
                    self::GRANT_TYPE_TOKEN,
                    self::GRANT_TYPE_CODE
                ));
        }

        $response = HttpClient::create()->request(
            'POST',
            \sprintf('%s/v1/oauth2/token', $this->authEndpoint),
            [
                'body' => $body,
            ]
        );

        $this->checkResponse($response);

        return Token::fromJson($response->getContent());
    }

    /**
     * Get consumption load curve between 2 dates for a usage point.
     *
     * Récupérer la puissance moyenne consommée quotidiennement,
     * sur l'intervalle de mesure du compteur (par défaut 30 min)
     */
    public function requestConsumptionLoadCurve(string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end, string $accessToken): MeteringData
    {
        return $this->requestMeteringData(
            'consumption_load_curve',
            MeteringData::TYPE_CONSUMPTION_LOAD_CURVE,
            $accessToken,
            $usagePointId,
            $start,
            $end
        );
    }

    /**
     * Get production load curve between 2 dates for a usage point.
     *
     * Récupérer la puissance moyenne produite quotidiennement,
     * sur l'intervalle de mesure du compteur (par défaut 30 min)
     */
    public function requestProductionLoadCurve(string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end, string $accessToken): MeteringData
    {
        return $this->requestMeteringData(
            'production_load_curve',
            MeteringData::TYPE_PRODUCTION_LOAD_CURVE,
            $accessToken,
            $usagePointId,
            $start,
            $end
        );
    }

    /**
     * Get daily consumption between 2 dates for a usage point.
     *
     * Récupérer la consommation quotidienne
     */
    public function requestDailyConsumption(string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end, string $accessToken): MeteringData
    {
        return $this->requestMeteringData(
            'daily_consumption',
            MeteringData::TYPE_DAILY_CONSUMPTION,
            $accessToken,
            $usagePointId,
            $start,
            $end
        );
    }

    /**
     * Get daily production between 2 dates for a usage point.
     *
     * Récupérer la production quotidienne
     */
    public function requestDailyProduction(string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end, string $accessToken): MeteringData
    {
        return $this->requestMeteringData(
            'daily_production',
            MeteringData::TYPE_DAILY_PRODUCTION,
            $accessToken,
            $usagePointId,
            $start,
            $end
        );
    }

    /**
     * Request MeterinData.
     */
    private function requestMeteringData(string $endpoint, string $dataType, string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
    {
        $response = HttpClient::create()->request(
            'GET',
            \sprintf('%s/v4/metering_data/%s', $this->meteringDataEndpoint, $endpoint),
            [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'auth_bearer' => $accessToken,
                'query' => [
                    'usage_point_id' => $usagePointId,
                    'start' => $start->format('Y-m-d'),
                    'end' => $end->format('Y-m-d'),
                ],
            ]
        );

        $this->checkResponse($response);

        return MeteringData::fromJson($response->getContent(), $dataType);
    }

    private function checkResponse(ResponseInterface $response): void
    {
        $code = $response->getStatusCode();

        if (200 !== $code) {
            switch ($code) {
                case 403:
                    throw new DataConnectConsentException($response->getContent(false), $code);
                case 404:
                    throw new DataConnectDataNotFoundException($response->getContent(false), $code);
                case 429:
                    throw new DataConnectQuotaExceededException($response->getContent(false), $code);
                default:
                    throw new DataConnectException($response->getContent(false), $code);
            }
        }
    }
}
