<?php

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\MeteringValue;
use DateInterval;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        self::assertSame("2019-05-06", $meteringValue->getDate()->format('Y-m-d'));
        self::assertSame(540.0, $meteringValue->getValue());
        self::assertEquals(new \DateInterval('P1D'), $meteringValue->getIntervalLength());
    }
}
