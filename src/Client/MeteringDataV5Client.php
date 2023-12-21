<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

use Aeneria\EnedisDataConnectApi\Model\MeteringData;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implements Metering Data V4
 *
 * @see https://datahub-enedis.fr/services-api/data-connect/documentation/metering-v5-consommation-quotidienne/
 */
class MeteringDataV5Client extends AbstractApiClient implements MeteringDataV5ClientInterface
{
    /** @var HttpClientInterface */
    private $httpClient;
    /** @var string */
    private $dataEndpoint;

    public function __construct(HttpClientInterface $httpClient, string $dataEndpoint)
    {
        $this->httpClient = $httpClient;
        $this->dataEndpoint = $dataEndpoint;
    }

    /**
     * {@inheritdoc}
     */
    public function requestConsumptionLoadCurve(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
    {
        return $this->requestMeteringData(
            'metering_data_clc/v5/consumption_load_curve',
            $accessToken,
            $usagePointId,
            $start,
            $end
        );
    }

    /**
     * {@inheritdoc}
     */
    public function requestDailyConsumption(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
    {
        return $this->requestMeteringData(
            'metering_data_dc/v5/daily_consumption',
            $accessToken,
            $usagePointId,
            $start,
            $end
        );
    }


    /**
     * Request MeterinData.
     */
    private function requestMeteringData(string $endpoint, string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
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
                    'start' => $start->format('Y-m-d'),
                    'end' => $end->format('Y-m-d'),
                ],
            ]
        );

        $this->checkResponse($response);

        return MeteringData::fromJson($response->getContent());
    }
}
