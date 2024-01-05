<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Meta-Client to access all API services
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/
 */
class DataConnectClient implements DataConnectClientInterface
{
    /** @var AuthorizeV1Client */
    private $authorizeV1Client;
    /** @var MeteringDataV5Client */
    private $meteringDataV5Client;
    /** @var CustomersV5Client */
    private $customersV5Client;

    public function __construct(
        HttpClientInterface $httpClient,
        string $authEndpoint,
        string $tokenEndpoint,
        string $dataEndpoint,
        string $clientId,
        string $clientSecret,
        string $redirectUri
    ) {
        $this->authorizeV1Client = new AuthorizeV1Client($httpClient, $authEndpoint, $tokenEndpoint, $clientId, $clientSecret, $redirectUri);
        $this->meteringDataV5Client = new MeteringDataV5Client($httpClient, $dataEndpoint);
        $this->customersV5Client = new CustomersV5Client($httpClient, $dataEndpoint);
    }

    public function getAuthorizeV1Client(): AuthorizeV1ClientInterface
    {
        return $this->authorizeV1Client;
    }

    public function getMeteringDataV5Client(): MeteringDataV5ClientInterface
    {
        return $this->meteringDataV5Client;
    }

    public function getCustomersV5Client(): CustomersV5ClientInterface
    {
        return $this->customersV5Client;
    }
}
