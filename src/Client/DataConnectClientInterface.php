<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

/**
 * Meta-Client to access all API services
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/
 */
interface DataConnectClientInterface
{
    public function getAuthorizeV1Client(): AuthorizeV1ClientInterface;

    public function getMeteringDataV5Client(): MeteringDataV5ClientInterface;

    public function getCustomersV5Client(): CustomersV5ClientInterface;
}
