<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Model;

/**
 * A representation of a DataConnect Token received from Data Connect API
 *
 * {
 *   "customer": {
 *     "customer_id": "1358019319",
 *     "usage_points": [
 *       {
 *         "usage_point": {
 *           "usage_point_id": "12345678901234",
 *           "usage_point_status": "com",
 *           "meter_type": "AMM",
 *           "usage_point_addresses": {
 *             "street": "2 bis rue du capitaine Flam",
 *             "locality": "lieudit Tourtouze",
 *             "postal_code": "32400",
 *             "insee_code": "32244",
 *             "city": "Maulichères",
 *             "country": "France",
 *             "geo_points": {
 *               "latitude": "43.687253",
 *               "longitude": "-0.087957",
 *               "altitude": "148"
 *             }
 *           }
 *         }
 *       }
 *     ]
 *   }
 * }
 *
 * We assume we only request 1 address.
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/customers/
 */
class Address
{
    public string|null $customerId;
    public string|null $usagePointId;
    public string|null $usagePointStatus;
    public string|null $meterType;
    public string|null $street;
    public string|null $locality;
    public string|null $postalCode;
    public string|null $inseeCode;
    public string|null $city;
    public string|null $country;
    public float|null $latitude;
    public float|null $longitude;
    public float|null $altitude;
    public string $rawData;

    public static function fromJson(string $jsonData): self
    {
        $address = new self();
        $address->rawData = $jsonData;

        try {
            $data = \json_decode($jsonData);
            $data = $data->customer;
            $address->customerId = $data->customer_id;

            $usagePointData = $data->usage_points[0]->usage_point ?? null;
            $address->usagePointId = \trim($usagePointData->usage_point_id ?? null);
            $address->usagePointStatus = $usagePointData->usage_point_status ?? null;
            $address->meterType = $usagePointData->meter_type ?? null;
            if (isset($usagePointData->usage_point_addresses) && ($usagePointAddresses = $usagePointData->usage_point_addresses)) {
                $address->street = $usagePointAddresses->street ?? null;
                $address->locality = $usagePointAddresses->locality ?? null;
                $address->postalCode = $usagePointAddresses->postal_code ?? null;
                $address->inseeCode = $usagePointAddresses->insee_code ?? null;
                $address->city = $usagePointAddresses->city ?? null;
                $address->country = $usagePointAddresses->country ?? null;
                $address->latitude = \floatval($usagePointAddresses->geo_points->latitude ?? null);
                $address->longitude = \floatval($usagePointAddresses->geo_points->longitude ?? null);
                $address->altitude = \floatval($usagePointAddresses->geo_points->altitude ?? null);
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(\sprintf(
                "La conversion vers l'objet Address a échoué : %s",
                $e->getMessage()
            ));
        }

        return $address;
    }

    public function __toString()
    {
        $parts = [];

        if ($this->street) {
            $parts[] = $this->street;
        }

        if ($this->locality) {
            $parts[] = $this->locality;
        }

        if ($this->postalCode) {
            $parts[] = $this->postalCode;
        }

        if ($this->city) {
            $parts[] = $this->city;
        }

        if ($this->country) {
            $parts[] = $this->country;
        }

        return \implode(", ", $parts);
    }
}
