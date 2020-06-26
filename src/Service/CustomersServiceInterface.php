<?php

namespace Aeneria\EnedisDataConnectApi\Service;

use Aeneria\EnedisDataConnectApi\Model\Address;

/**
 * Implements DataConnect Customers API
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/customers/
 */
interface CustomersServiceInterface
{
    public function requestUsagePointAdresse(string $accessToken, string $usagePointId): Address;
}
