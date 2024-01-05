<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

use Aeneria\EnedisDataConnectApi\Model\Address;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implements Customers API
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/customers/
 */
class CustomersV5Client extends AbstractApiClient implements CustomersV5ClientInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $dataEndpoint
    ) { }

    /**
     * {@inheritdoc}
     */
    public function requestUsagePointAdresse(string $accessToken, string $usagePointId): Address
    {
        $response = $this->requestCustomersData('customers_upa/v5/usage_points/addresses', $accessToken, $usagePointId);

        return Address::fromJson($response);
    }

    private function requestCustomersData(string $endpoint, string $accessToken, string $usagePointId): string
    {
        $response = $this->httpClient->request(
            'GET',
            \sprintf('%s/%s', $this->dataEndpoint, $endpoint),
            [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'auth_bearer' => $accessToken,
                'query' => [
                    'usage_point_id' => $usagePointId,
                ],
            ]
        );

        $this->checkResponse($response);

        return $response->getContent();
    }
}
