<?php

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
    /** @var float */
    private $value;

    /** @var \DateTimeImmutable */
    private $date;

    /** @var \DateInterval */
    private $intervalLength;

    public static function fromStdClass(\stdClass $data): self
    {
        $meteringValue = new self();

        try {
            $meteringValue->value = $data->value;
            $meteringValue->date = \DateTimeImmutable::createFromFormat('!Y-m-d h:i:s', $data->date) ?: \DateTimeImmutable::createFromFormat('!Y-m-d', $data->date);
            $meteringValue->intervalLength = $data->interval_length ? new \DateInterval($data->interval_length) : null;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(\sprintf(
                "La conversion vers l'objet MeteringValue a échoué : %s",
                $e->getMessage()
            ));
        }

        return $meteringValue;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function getIntervalLength(): ?\DateInterval
    {
        return $this->intervalLength;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }
}
