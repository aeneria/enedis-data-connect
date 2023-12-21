<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Model;

/**
 * A representation of a Result received from Data Connect API
 *
 * {
 *    "value": "540",
 *    "date": "2019-05-06"
 * }
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/metering-data-v4/
 */
class MeteringValue
{
    public float $value;
    public \DateTimeImmutable $date;
    public ?\DateInterval $intervalLength = null;
    public \stdClass $rawData;

    public static function fromStdClass(\stdClass $data): self
    {
        $meteringValue = new self();
        $meteringValue->rawData = $data;

        try {
            $meteringValue->value = \floatval($data->value);
            $meteringValue->date = \DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $data->date) ?: \DateTimeImmutable::createFromFormat('!Y-m-d', $data->date);
            $meteringValue->intervalLength = $data->interval_length ? new \DateInterval($data->interval_length) : null;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(\sprintf(
                "La conversion vers l'objet MeteringValue a échoué : %s",
                $e->getMessage()
            ));
        }

        return $meteringValue;
    }
}
