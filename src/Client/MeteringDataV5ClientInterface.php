<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

use Aeneria\EnedisDataConnectApi\Model\MeteringData;

/**
 * Implements DataConnect Metering Data V4
 *
 * @see https://datahub-enedis.fr/services-api/data-connect/documentation/metering-v5-consommation-quotidienne/
 */
interface MeteringDataV5ClientInterface
{
    /**
     * Get consumption load curve between 2 dates for a usage point.
     *
     * Récupérer la puissance moyenne consommée quotidiennement,
     * sur l'intervalle de mesure du compteur (par défaut 30 min)
     */
    public function requestConsumptionLoadCurve(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData;

    /**
     * Get daily consumption between 2 dates for a usage point.
     *
     * Récupérer la consommation quotidienne
     */
    public function requestDailyConsumption(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData;
}
