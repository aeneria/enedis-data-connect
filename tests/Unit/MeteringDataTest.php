<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\MeteringData;
use PHPUnit\Framework\TestCase;

final class MeteringDataTest extends TestCase
{
    public function testHydratation()
    {
        $data = <<<JSON
        {
          "meter_reading": {
            "usage_point_id": "16401220101758",
            "start": "2019-05-06",
            "end": "2019-05-12",
            "quality": "BRUT",
            "reading_type": {
              "measurement_kind": "energy",
              "measuring_period": "P1D",
              "unit": "Wh",
              "aggregate": "sum"
            },
            "interval_reading": [
              {
                "value": "540",
                "date": "2019-05-06"
              }
            ]
          }
        }
        JSON;

        $meteringData = MeteringData::fromJson($data);

        self::assertInstanceOf(MeteringData::class, $meteringData);
        self::assertSame("16401220101758", $meteringData->usagePointId);
        self::assertSame("2019-05-06", $meteringData->start->format('Y-m-d'));
        self::assertSame("2019-05-12", $meteringData->end->format('Y-m-d'));
        self::assertSame("Wh", $meteringData->unit);
        self::assertCount(1, $meteringData->values);
    }
}
