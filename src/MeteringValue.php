<?php

namespace Aeneria\EnedisDataConnectApi;

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
    /** @var float */
    private $value;

    /** @var \DateTimeImmutable */
    private $date;

    static public function fromArray(array $data): self
    {

        $meteringValue = new self();
        $this->value = $data["value"];
        $this->date = \DateTimeImmutable::createFromFormat('Y-m-d', $data["date"]);

        return $meteringValue;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}