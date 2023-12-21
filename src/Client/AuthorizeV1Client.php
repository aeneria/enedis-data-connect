<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

use Aeneria\EnedisDataConnectApi\Model\Token;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implements AuthorizeV1 API
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/authorize-v1/
 */
class AuthorizeV1Client extends AbstractApiClient implements AuthorizeV1ClientInterface
{
    public const GRANT_TYPE_CODE = 'authorization_code';
    public const GRANT_TYPE_TOKEN = 'refresh_token';

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var string */
    private $authEndpoint;
    /** @var string */
    private $tokenEndpoint;

    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var string */
    private $redirectUri;

    public function __construct(
        HttpClientInterface $httpClient,
        string $authEndpoint,
        string $tokenEndpoint,
        string $clientId,
        string $clientSecret,
        string $redirectUri
    ) {
        $this->httpClient = $httpClient;

        $this->authEndpoint = $authEndpoint;
        $this->tokenEndpoint = $tokenEndpoint;

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function requestAuthorizationToken(): Token
    {
        $body = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $response = $this->httpClient->request(
            'POST',
            \sprintf('%s/oauth2/v3/token', $this->authEndpoint),
            [
                'body' => $body,
            ]
        );

        $this->checkResponse($response);

        return Token::fromJson($response->getContent());
    }
}
