<?php

namespace Aeneria\EnedisDataConnectApi\Service;

/**
 * Meta-Service to access all API services
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/
 */
class DataConnectService
{
    /** @var AuthorizeV1Service */
    private $authorizeV1Service;
    /** @var MeteringDataV4Service */
    private $meteringDataV4Service;
    /** @var CustomersService */
    private $customersService;


    public function __construct(string $authEndpoint, string $dataEndpoint, string $clientId, string $clientSecret, string $redirectUri)
    {
        $this->authorizeV1Service = new AuthorizeV1Service($authEndpoint, $clientId, $clientSecret, $redirectUri);
        $this->meteringDataV4Service = new MeteringDataV4Service($dataEndpoint);
        $this->customersService = new CustomersService($dataEndpoint);
    }

    public function getAuthorizeV1Service(): AuthorizeV1Service
    {
        return $this->authorizeV1Service;
    }

    public function getMeteringDataV4Service(): MeteringDataV4Service
    {
        return $this->meteringDataV4Service;
    }

    public function getCustomersService(): CustomersService
    {
        return $this->customersService;
    }
}
