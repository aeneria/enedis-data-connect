<?php

namespace Aeneria\EnedisDataConnectApi\Services;

use Aeneria\EnedisDataConnectApi\Exception\DataConnectConsentException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectQuotaExceededException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectDataNotFoundException;
use Aeneria\EnedisDataConnectApi\MeteringData;
use Aeneria\EnedisDataConnectApi\Token;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Implements DataConnect API from datahub-enedis
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/authorize-v1/
 */
class DataConnectService
{
    private $authEndpoint;
    private $meteringDataEndpoint;

    private $clientId;
    private $clientSecret;

    public function __construct (string $authEndpoint, string $meteringDataEndpoint, string $clientId, string $clientSecret)
    {
        $this->authEndpoint = $authEndpoint;
        $this->meteringDataEndpoint = $meteringDataEndpoint;

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get a redirect response to DataConnect consent page.
     *
     * @var string $duration Durée du consentement demandé par l’application,
     * au format ISO 8601. Cette durée sera affichée au consommateur et ne peut
     * excéder 3 ans.
     *
     * @var string $state Paramètre de sécurité permettant de maintenir l’état
     * entre la requête et la redirection.
     */
    public function getRedirectResponseToConsentPage(string $duration, string $state): RedirectResponse
    {
        return RedirectResponse::create(
            \sprintf('%s/dataconnect/v1/oauth2/authorize', $this->authEndpoint),
            RedirectResponse::HTTP_FOUND,
            [
                'query' => [
                    'client_id' => $this->clientId,
                    'response_type' => 'code',
                    'state' => $state,
                    'duration' => $duration
                ]
            ]
        );
    }

    /**
     * Get DataConnectToken from a grant code.
     */
    public function requestDataConnectTokensFromCode(string $code): Token
    {
        return $this->requestToken('authorization_code', '', $code);
    }

    /**
     * Get DataConnectToken from a refreshToken.
     */
    public function requestDataConnectTokensFromRefreshToken(string $refreshToken): Token
    {
        return $this->requestToken('refresh_token', $refreshToken);
    }

    private function requestToken(string $grantType, string $refreshToken = '', string $code = ''): Token
    {
        $response = HttpClient::create()->request(
            'POST',
            \sprintf('%s/v1/oauth2/token', $this->authEndpoint),
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'query' => [
                    'client_id' => $this->clientId,
                    'client_id' => $this->clientSecret,
                    'grant_type' => $grantType,
                    'refresh_token' => $refreshToken,
                    'code' => $code,
                ]
            ]
        );

        $this->checkResponse($response);

        return Token::fromJson($response->getContent());
    }

    /**
     * Get load curve between 2 dates for a usage point.
     *
     * Récupérer la puissance moyenne consommée quotidiennement,
     * sur l'intervalle de mesure du compteur (par défaut 30 min)
     */
    public function requestConsumptionLoadCurve(string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end, string $accessToken): MeteringData
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
     * Get daily consumption between 2 dates for a usage point.
     *
     * Récupérer la consommation quotidienne
     */
    public function requestDailyConsumption(string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end, string $accessToken): MeteringData
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
                    'Authorization' => \sprintf('Bearer %s', $accessToken),
                ],
                'query' => [
                    'usage_point_id' => $usagePointId,
                    'start' => $start->format('Y-M-d'),
                    'end' => $end->format('Y-M-d'),
                ]
            ]
        );

        $this->checkResponse($response);

        return MeteringData::fromJson($response->getContent(), MeteringData::TYPE_DAILY_CONSUMPTION);
    }

    private function checkResponse(ResponseInterface $response): void
    {
        $code = $response->getStatusCode();

        if (200 !== $code) {
            $result = \json_decode($response->getContent());
            $message = $result["error_description"] ?? '';

            switch ($code) {
                case 403:
                    throw new DataConnectConsentException($message, $code);
                case 404:
                    throw new DataConnectDataNotFoundException($message, $code);
                case 429:
                    throw new DataConnectQuotaExceededException($message, $code);
                default:
                    throw new DataConnectException($response->getContent, $response->getStatusCode());
            }
        }
    }
}