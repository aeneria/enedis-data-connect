<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Client;

class MockDataConnectClient implements DataConnectClientInterface
{
    /** @var AuthorizeV1ClientInterface */
    private $authorizeV1Client;
    /** @var MeteringDataV5ClientInterface */
    private $meteringDataV5Client;
    /** @var CustomersV5ClientInterface */
    private $customersV5Client;

    public function __construct()
    {
        $this->authorizeV1Client = new MockAuthorizeV1Client();
        $this->meteringDataV5Client = new MockMeteringDataV5Client();
        $this->customersV5Client = new MockCustomersV5Client();
    }

    public function getAuthorizeV1Client(): AuthorizeV1ClientInterface
    {
        return $this->authorizeV1Client;
    }

    public function getMeteringDataV5Client(): MeteringDataV5ClientInterface
    {
        return $this->meteringDataV5Client;
    }

    public function getCustomersV5Client(): CustomersV5ClientInterface
    {
        return $this->customersV5Client;
    }
}
