<?php

namespace Aeneria\EnedisDataConnectApi;

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
    const TYPE_CONSUMPTION_LOAD_CURVE = 'CONSUMPTION_LOAD_CURVE';
    const TYPE_DAILY_CONSUMPTION = 'DAILY_CONSUMPTION';

    /** @var string */
    private $usagePointId;

    /** @var \DateTimeImmutable */
    private $start;

    /** @var \DateTimeImmutable */
    private $end;

    /** @var string */
    private $unit;

    /** @var string */
    private $dataType;

    /** @var DataConnectValue[] */
    private $values = [];

    static public function fromJson(string $jsonData, string $dataType): self
    {
        $data = \json_decode($jsonData);

        $meteringData = new self();
        $meteringData->dataType = $dataType;
        $meteringData->usagePointId = $data["usage_point_id"];
        $meteringData->start = \DateTimeImmutable::createFromFormat('Y-m-d', $data["start"]);
        $meteringData->end = \DateTimeImmutable::createFromFormat('Y-m-d', $data["end"]);
        $meteringData->unit = $data["reading_type"]["unit"];

        foreach( $data['interval_reading'] as $value) {
            $meteringData->values[] = MeteringValue::fromArray($value);
        }

        return $meteringData;
    }


    public function getUsagePointId(): string
    {
        return $this->usagePointId;
    }

    public function getStart(): \DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd(): \DateTimeImmutable
    {
        return $this->end;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}