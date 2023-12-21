<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\MeteringData;
use Aeneria\EnedisDataConnectApi\Client\MeteringDataV5Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class MeteringDataV5ClientTest extends TestCase
{
    public function testRequestConsumptionLoadCurve()
    {
        $json = <<<JSON
        {
          "meter_reading": {
            "usage_point_id": "16401220101758",
            "start": "2019-05-06",
            "end": "2019-05-12",
            "quality": "BRUT",
            "reading_type": {
              "measurement_kind": "power",
              "unit": "W",
              "aggregate": "average"
            },
            "interval_reading": [
              {
                "value": "540",
                "date": "2019-05-06 03:00:00",
                "interval_length": "PT30M",
                "measure_type": "B"
              }
            ]
          }
        }
        JSON;
        $data = MeteringData::fromJson($json);

        $httpClient = new MockHttpClient(
            new MockResponse($json)
        );

        $service = new MeteringDataV5Client(
            $httpClient,
            'http://endpoint.fr'
        );

        $dataFromService = $service->requestConsumptionLoadCurve(
            'accessToken',
            'usagePoint',
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        self::assertEquals($data, $dataFromService);
    }

    public function testRequestDailyConsumption()
    {
        $json = <<<JSON
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
        $data = MeteringData::fromJson($json);

        $httpClient = new MockHttpClient(
            new MockResponse($json)
        );

        $service = new MeteringDataV5Client(
            $httpClient,
            'http://endpoint.fr'
        );

        $dataFromService = $service->requestDailyConsumption(
            'accessToken',
            'usagePoint',
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        self::assertEquals($data, $dataFromService);
    }
}
