<?php

namespace Aeneria\EnedisDataConnectApi\Service;

use Aeneria\EnedisDataConnectApi\Model\Address;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Implements Customers API
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/customers/
 */
class CustomersService extends AbstractApiService implements CustomersServiceInterface
{
    private $dataEndpoint;

    public function __construct(string $dataEndpoint)
    {
        $this->dataEndpoint = $dataEndpoint;
    }

    /**
     * @inheritdoc
     */
    public function requestUsagePointAdresse(string $accessToken, string $usagePointId): Address
    {
        $response = $this->requestCustomersData('usage_points/addresses', $accessToken, $usagePointId);

        return Address::fromJson($response);
    }

    private function requestCustomersData(string $endpoint, string $accessToken, string $usagePointId): string
    {
        $response = HttpClient::create()->request(
            'GET',
            \sprintf('%s/v3/customers/%s', $this->dataEndpoint, $endpoint),
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
