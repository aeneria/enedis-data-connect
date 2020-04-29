<?php

namespace Aeneria\EnedisDataConnectApi\Service;

use Aeneria\EnedisDataConnectApi\Model\MeteringData;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Implements Metering Data V4
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/metering-data-v4/
 */
class MeteringDataV4Service extends AbstractApiService
{
    /** @var string */
    private $dataEndpoint;

    public function __construct(string $dataEndpoint)
    {
        $this->dataEndpoint = $dataEndpoint;
    }

    /**
     * Get consumption load curve between 2 dates for a usage point.
     *
     * Récupérer la puissance moyenne consommée quotidiennement,
     * sur l'intervalle de mesure du compteur (par défaut 30 min)
     */
    public function requestConsumptionLoadCurve(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
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
    public function requestProductionLoadCurve(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
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
    public function requestDailyConsumption(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
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
    public function requestDailyProduction(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
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
            \sprintf('%s/v4/metering_data/%s', $this->dataEndpoint, $endpoint),
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
}
