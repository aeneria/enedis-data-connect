<?php

namespace Aeneria\EnedisDataConnectApi\Service;

class MockDataConnectService implements DataConnectServiceInterface
{
    /** @var AuthorizeV1Service */
    private $authorizeV1Service;
    /** @var MeteringDataV4Service */
    private $meteringDataV4Service;
    /** @var CustomersService */
    private $customersService;

    public function __construct()
    {
        $this->authorizeV1Service = new MockAuthorizeV1Service();
        $this->meteringDataV4Service = new MockMeteringDataV4Service();
        $this->customersService = new MockCustomersService();
    }

    public function getAuthorizeV1Service(): AuthorizeV1ServiceInterface
    {
        return $this->authorizeV1Service;
    }

    public function getMeteringDataV4Service(): MeteringDataV4ServiceInterface
    {
        return $this->meteringDataV4Service;
    }

    public function getCustomersService(): CustomersServiceInterface
    {
        return $this->customersService;
    }
}
