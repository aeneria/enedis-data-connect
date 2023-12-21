<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\Address;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class AddressTest extends TestCase
{
    public function testHydratation()
    {
        $data = <<<JSON
        {
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
        }
        JSON;

        $address = Address::fromJson($data);

        self::assertInstanceOf(Address::class, $address);
        self::assertSame('1358019319', $address->customerId);
        self::assertSame('12345678901234', $address->usagePointId);
        self::assertSame('com', $address->usagePointStatus);
        self::assertSame('AMM', $address->meterType);
        self::assertSame('2 bis rue du capitaine Flam', $address->street);
        self::assertSame('lieudit Tourtouze', $address->locality);
        self::assertSame('32400', $address->postalCode);
        self::assertSame('32244', $address->inseeCode);
        self::assertSame('Maulichères', $address->city);
        self::assertSame('France', $address->country);
        self::assertSame(43.687253, $address->latitude);
        self::assertSame(-0.087957, $address->longitude);
        self::assertSame(148.0, $address->altitude);
        self::assertSame("2 bis rue du capitaine Flam, lieudit Tourtouze, 32400, Maulichères, France", $address . "");
    }

    public function testSerialization()
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $data = <<<JSON
          {
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
          }
        JSON;

        $address = Address::fromJson($data);

        $deserializedAddress = $serializer->deserialize(
            $serializer->serialize($address, 'json'),
            Address::class,
            'json'
        );

        self::assertInstanceOf(Address::class, $deserializedAddress);
        self::assertSame('1358019319', $deserializedAddress->customerId);
        self::assertSame('12345678901234', $deserializedAddress->usagePointId);
        self::assertSame('com', $deserializedAddress->usagePointStatus);
        self::assertSame('AMM', $deserializedAddress->meterType);
        self::assertSame('2 bis rue du capitaine Flam', $deserializedAddress->street);
        self::assertSame('lieudit Tourtouze', $deserializedAddress->locality);
        self::assertSame('32400', $deserializedAddress->postalCode);
        self::assertSame('32244', $deserializedAddress->inseeCode);
        self::assertSame('Maulichères', $deserializedAddress->city);
        self::assertSame('France', $deserializedAddress->country);
        self::assertSame(43.687253, $deserializedAddress->latitude);
        self::assertSame(-0.087957, $deserializedAddress->longitude);
        self::assertSame(148.0, $deserializedAddress->altitude);
        self::assertSame("2 bis rue du capitaine Flam, lieudit Tourtouze, 32400, Maulichères, France", $address . "");
    }
}
