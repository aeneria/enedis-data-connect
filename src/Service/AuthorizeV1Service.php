<?php

namespace Aeneria\EnedisDataConnectApi\Service;

use Aeneria\EnedisDataConnectApi\Model\Token;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Implements AuthorizeV1 API
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/authorize-v1/
 */
class AuthorizeV1Service extends AbstractApiService implements AuthorizeV1ServiceInterface
{
    const GRANT_TYPE_CODE = 'authorization_code';
    const GRANT_TYPE_TOKEN = 'refresh_token';

    /** @var string */
    private $authEndpoint;

    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var string */
    private $redirectUri;

    public function __construct(string $authEndpoint, string $clientId, string $clientSecret, string $redirectUri)
    {
        $this->authEndpoint = $authEndpoint;

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function requestTokenFromCode(string $code): Token
    {
        return $this->requestToken(self::GRANT_TYPE_CODE, $code);
    }

    /**
     * @inheritdoc
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
            'redirect_uri' => $this->redirectUri,
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
}
