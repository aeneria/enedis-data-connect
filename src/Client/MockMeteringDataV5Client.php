<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

use Aeneria\EnedisDataConnectApi\Model\MeteringData;

class MockMeteringDataV5Client extends AbstractApiClient implements MeteringDataV5ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function requestConsumptionLoadCurve(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
    {
        $json = '{"meter_reading": {';
        $json .= '"usage_point_id": "' . $usagePointId . '", ';
        $json .= '"start": "' . $start->format('Y-m-d') . '", ';
        $json .= '"end": "' . $start->format('Y-m-d') . '", ';
        $json .= '"quality": "BRUT", ';
        $json .= '"reading_type": {"measurement_kind": "power", "unit": "W", "aggregate": "average"}, ';
        $json .= '"interval_reading": [';

        $currentDatetime = \DateTime::createFromInterface($start);
        $data = [];
        while ($currentDatetime < $end) {
            $data[] = '{"value": "100", "date": "' . $currentDatetime->format('Y-m-d H:i:s') . '", "interval_length": "PT30M", "measure_type": "B"}';
            $currentDatetime->add(new \DateInterval('PT30M'));
        }
        $json .= \implode(', ', $data);

        $json .= ']}}';

        return MeteringData::fromJson($json);
    }

    /**
     * {@inheritdoc}
     */
    public function requestDailyConsumption(string $accessToken, string $usagePointId, \DateTimeInterface $start, \DateTimeInterface $end): MeteringData
    {
        $json = '{"meter_reading": {';
        $json .= '"usage_point_id": "' . $usagePointId . '", ';
        $json .= '"start": "' . $start->format('Y-m-d') . '", ';
        $json .= '"end": "' . $start->format('Y-m-d') . '", ';
        $json .= '"quality": "BRUT", ';
        $json .= '"reading_type": {"measurement_kind": "energy", "measuring_period": "P1D", "unit": "Wh", "aggregate": "sum"}, ';
        $json .= '"interval_reading": [';

        $currentDatetime = \DateTime::createFromInterface($start);
        $data = [];
        while ($currentDatetime < $end) {
            $data[] = '{"value": "100", "date": "' . $currentDatetime->format('Y-m-d') . '"}';
            $currentDatetime->add(new \DateInterval('P1D'));
        }

        $json .= \implode(', ', $data);
        $json .= ']}}';

        return MeteringData::fromJson($json);
    }
}
