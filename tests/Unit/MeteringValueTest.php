<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\MeteringValue;
use PHPUnit\Framework\TestCase;

final class MeteringValueTest extends TestCase
{
    public function testHydratation()
    {
        $data = <<<JSON
        {
          "value": "540",
          "date": "2019-05-06",
          "interval_length": "P1D"
        }
        JSON;

        $meteringValue = MeteringValue::fromStdClass(\json_decode($data));

        self::assertInstanceOf(MeteringValue::class, $meteringValue);
        self::assertSame("2019-05-06", $meteringValue->date->format('Y-m-d'));
        self::assertSame(540.0, $meteringValue->value);
        self::assertEquals(new \DateInterval('P1D'), $meteringValue->intervalLength);
    }
}
