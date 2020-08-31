<?php

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\Address;
use Aeneria\EnedisDataConnectApi\Service\CustomersService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class CustomersServiceTest extends TestCase
{
    public function testRequestUsagePointAdresse()
    {
        $json = <<<JSON
        {
          "customer": {
            "customer_id": "1358019319",
            "usage_points": [
              {
                "usage_point": {
                  "usage_point_id": "123132132132",
                  "usage_point_status": "com",
                  "meter_type": "AMM",
                  "usage_point_addresses": {
                    "street": "2 bis rue du capitaine Flam",
                    "locality": "lieudit Tourtouze",
                    "postal_code": "32400",
                    "insee_code": "32244",
                    "city": "Maulichères",
                    "country": "France",
                    "geo_points": {
                      "latitude": "43.687253",
                      "longitude": "-0.087957",
                      "altitude": "148"
                    }
                  }
                }
              }
            ]
          }
        }
        JSON;
        $address = Address::fromJson($json);

        $httpClient = new MockHttpClient(
            new MockResponse($json)
        );

        $service = new CustomersService(
            $httpClient,
            'http://endpoint.fr'
        );

        $addressFromService = $service->requestUsagePointAdresse('accessToken', 'usagePoint');

        self::assertEquals($address, $addressFromService);
    }
}
