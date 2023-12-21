<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

use Aeneria\EnedisDataConnectApi\Model\Address;

/**
 * Implements DataConnect Customers API
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/customers/
 */
interface CustomersV5ClientInterface
{
    public function requestUsagePointAdresse(string $accessToken, string $usagePointId): Address;
}
