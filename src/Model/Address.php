<?php

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
    /** @var string|null */
    private $customerId;

    /** @var string|null */
    private $usagePointId;

    /** @var string|null */
    private $usagePointStatus;

    /** @var string|null */
    private $meterType;

    /** @var string|null */
    private $street;

    /** @var string|null */
    private $locality;

    /** @var string|null */
    private $postalCode;

    /** @var string|null */
    private $inseeCode;

    /** @var string|null */
    private $city;

    /** @var string|null */
    private $country;

    /** @var float|null */
    private $latitude;

    /** @var float|null */
    private $longitude;

    /** @var float|null */
    private $altitude;

    public static function fromJson(string $jsonData): self
    {
        $address = new self();

        try {
            $data = \json_decode($jsonData);
            $data = $data[0]->customer;
            $address->customerId = $data->customer_id;

            $usagePointData = $data->usage_points[0]->usage_point;
            $address->usagePointId = \trim($usagePointData->usage_point_id);
            $address->usagePointStatus = $usagePointData->usage_point_status;
            $address->meterType = $usagePointData->meter_type;
            $address->street = $usagePointData->usage_point_addresses->street;
            $address->locality = $usagePointData->usage_point_addresses->locality;
            $address->postalCode = $usagePointData->usage_point_addresses->postal_code;
            $address->inseeCode = $usagePointData->usage_point_addresses->insee_code;
            $address->city = $usagePointData->usage_point_addresses->city;
            $address->country = $usagePointData->usage_point_addresses->country;
            $address->latitude = \floatval($usagePointData->usage_point_addresses->geo_points->latitude);
            $address->longitude = \floatval($usagePointData->usage_point_addresses->geo_points->longitude);
            $address->altitude = \floatval($usagePointData->usage_point_addresses->geo_points->altitude);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(\sprintf(
                "La conversion vers l'objet Address a échoué : %s",
                $e->getMessage()
            ));
        }

        return $address;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function getUsagePointId(): ?string
    {
        return $this->usagePointId;
    }

    public function getUsagePointStatus(): ?string
    {
        return $this->usagePointStatus;
    }

    public function getMeterType(): ?string
    {
        return $this->meterType;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getInseeCode(): ?string
    {
        return $this->inseeCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
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