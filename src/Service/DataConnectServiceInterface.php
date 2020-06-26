<?php

namespace Aeneria\EnedisDataConnectApi\Service;

/**
 * Meta-Service to access all API services
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/
 */
interface DataConnectServiceInterface
{
    public function getAuthorizeV1Service(): AuthorizeV1ServiceInterface;

    public function getMeteringDataV4Service(): MeteringDataV4ServiceInterface;

    public function getCustomersService(): CustomersServiceInterface;
}
