<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Model;

/**
 * A representation of a Result received from Data Connect API
 *
 * {
 *   "meter_reading": {
 *     "usage_point_id": "16401220101758",
 *     "start": "2019-05-06",
 *     "end": "2019-05-12",
 *     "quality": "BRUT",
 *     "reading_type": {
 *       "measurement_kind": "energy",
 *       "measuring_period": "P1D",
 *       "unit": "Wh",
 *       "aggregate": "sum"
 *     },
 *     "interval_reading": [
 *       {
 *         "value": "540",
 *         "date": "2019-05-06"
 *       }
 *     ]
 *   }
 * }
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/metering-data-v4/
 */
class MeteringData
{
    public string $usagePointId;
    public \DateTimeImmutable $start;
    public \DateTimeImmutable $end;
    public string $unit;
    /** @var MeteringValue[] */
    public array $values = [];
    public string $rawData;

    public static function fromJson(string $jsonData): self
    {
        $meteringData = new self();
        $meteringData->rawData = $jsonData;

        try {
            $data = \json_decode($jsonData);

            $meteringData->usagePointId = $data->meter_reading->usage_point_id;
            $meteringData->start = \DateTimeImmutable::createFromFormat('!Y-m-d', $data->meter_reading->start);
            $meteringData->end = \DateTimeImmutable::createFromFormat('!Y-m-d', $data->meter_reading->end);
            $meteringData->unit = $data->meter_reading->reading_type->unit;

            // Les données journalière on une info de péride de mesure, pour la courbe de charge, cette
            // info est située au niveau de chaque mesure (parce qu'elle peut varier). Pour rendre le tout
            // homogène, on déplace cette valuer au niveau de la mesure si elle existe.
            $period = $data->meter_reading->reading_type->measuring_period ?? null;

            foreach ($data->meter_reading->interval_reading as $value) {
                if ($period && !isset($value->interval_length)) {
                    $value->interval_length = $period;
                }
                $meteringData->values[] = MeteringValue::fromStdClass($value);
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(\sprintf(
                "La conversion vers l'objet MeteringData a échoué : %s",
                $e->getMessage()
            ));
        }

        return $meteringData;
    }
}
