<?php

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\Address;
use PHPUnit\Framework\TestCase;

final class AddressTest extends TestCase
{
    public function testHydratation()
    {
        $data = <<<JSON
        [{
          "customer": {
            "customer_id": "1358019319",
            "usage_points": [
              {
                "usage_point": {
                  "usage_point_id": "12345678901234",
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
        }]
        JSON;

        $address = Address::fromJson($data);

        self::assertInstanceOf(Address::class, $address);
        self::assertSame('1358019319', $address->getCustomerId());
        self::assertSame('12345678901234', $address->getUsagePointId());
        self::assertSame('com', $address->getUsagePointStatus());
        self::assertSame('AMM', $address->getMeterType());
        self::assertSame('2 bis rue du capitaine Flam', $address->getStreet());
        self::assertSame('lieudit Tourtouze', $address->getLocality());
        self::assertSame('32400', $address->getPostalCode());
        self::assertSame('32244', $address->getInseeCode());
        self::assertSame('Maulichères', $address->getCity());
        self::assertSame('France', $address->getCountry());
        self::assertSame(43.687253, $address->getLatitude());
        self::assertSame(-0.087957, $address->getLongitude());
        self::assertSame(148.0, $address->getAltitude());
        self::assertSame("2 bis rue du capitaine Flam, lieudit Tourtouze, 32400, Maulichères, France", $address . "");
    }
}
