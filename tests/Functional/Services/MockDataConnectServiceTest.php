<?php

namespace Aeneria\EnedisDataConnectApi\Tests\Functional\Services;

use Aeneria\EnedisDataConnectApi\Model\Address;
use Aeneria\EnedisDataConnectApi\Model\MeteringData;
use Aeneria\EnedisDataConnectApi\Model\Token;
use Aeneria\EnedisDataConnectApi\Service\MockAuthorizeV1Service;
use Aeneria\EnedisDataConnectApi\Service\MockCustomersService;
use Aeneria\EnedisDataConnectApi\Service\MockDataConnectService;
use Aeneria\EnedisDataConnectApi\Service\MockMeteringDataV4Service;
use PHPUnit\Framework\TestCase;

final class MockDataConnectServiceTest extends TestCase
{
    /**
     * That's a bit ugly but we test all DataConnectService methods in a single test
     * to avoid calling Enedis API to many times.
     */
    public function testMockDataConnectService()
    {
        $dataConnect = new MockDataConnectService();

        // Test Authorize V1 API
        $token = $this->gettingConsent($dataConnect->getAuthorizeV1Service());
        $token = $this->gettingAccessToken($dataConnect->getAuthorizeV1Service(), $token);

        // Test Metering Data V4 API
        $this->gettingConsumptionData($dataConnect->getMeteringDataV4Service(), $token);
        // My client Id currently can't get production data, it's outside my authorized scope !
        $this->gettingProductionData($dataConnect->getMeteringDataV4Service(), $token);

        // Test Customers API
        $this->gettingCustomerData($dataConnect->getCustomersService(), $token);
    }

    private function gettingConsent(MockAuthorizeV1Service $service): Token
    {
        $token = $service->requestTokenFromCode('');

        self::assertInstanceOf(Token::class, $token);
        self::assertNotNull($token->getRefreshToken());

        return $token;
    }

    private function gettingAccessToken(MockAuthorizeV1Service $service, Token $token): Token
    {
        $token = $service->requestTokenFromRefreshToken($token->getRefreshToken());

        self::assertInstanceOf(Token::class, $token);
        self::assertNotNull($token->getRefreshToken());
        self::assertNotNull($token->getAccessToken());

        return $token;
    }

    private function gettingConsumptionData(MockMeteringDataV4Service $service, Token $token): void
    {
        $meteringData = $service->requestDailyConsumption(
            $token->getAccessToken(),
            $token->getUsagePointsId(),
            new \DateTimeImmutable('8 days ago midnight'),
            new \DateTimeImmutable('yesterday midnight')
        );

        self::assertInstanceOf(MeteringData::class, $meteringData);
        self::assertSame(MeteringData::TYPE_DAILY_CONSUMPTION, $meteringData->getDataType());
        self::assertSame(7, \count($meteringData->getValues()));
        self::assertSame('Wh', $meteringData->getUnit());

        $meteringValue = $meteringData->getValues()[0];
        self::assertEquals(new \DateInterval('P1D'), $meteringValue->getIntervalLength());
        self::assertNotNull($meteringValue->getValue());
        self::assertInstanceOf(\DateTimeInterface::class, $meteringValue->getDate());

        $meteringData = $service->requestConsumptionLoadCurve(
            $token->getAccessToken(),
            $token->getUsagePointsId(),
            new \DateTimeImmutable('2 days ago midnight'),
            new \DateTimeImmutable('yesterday midnight')
        );

        self::assertInstanceOf(MeteringData::class, $meteringData);
        self::assertSame(MeteringData::TYPE_CONSUMPTION_LOAD_CURVE, $meteringData->getDataType());
        self::assertGreaterThan(24, \count($meteringData->getValues()));
        self::assertSame('W', $meteringData->getUnit());

        $meteringValue = $meteringData->getValues()[0];
        self::assertEquals($meteringValue->getIntervalLength(), new \DateInterval('PT30M'));
        self::assertNotNull($meteringValue->getValue());
        self::assertInstanceOf(\DateTimeInterface::class, $meteringValue->getDate());
    }

    private function gettingProductionData(MockMeteringDataV4Service $service, Token $token): void
    {
        $meteringData = $service->requestDailyProduction(
            $token->getAccessToken(),
            $token->getUsagePointsId(),
            new \DateTimeImmutable('8 days ago midnight'),
            new \DateTimeImmutable('yesterday midnight')
        );

        self::assertInstanceOf(MeteringData::class, $meteringData);
        self::assertSame(MeteringData::TYPE_DAILY_PRODUCTION, $meteringData->getDataType());
        self::assertSame(7, \count($meteringData->getValues()));
        self::assertSame('Wh', $meteringData->getUnit());

        $meteringValue = $meteringData->getValues()[0];
        self::assertEquals(new \DateInterval('P1D'), $meteringValue->getIntervalLength());
        self::assertNotNull($meteringValue->getValue());
        self::assertInstanceOf(\DateTimeInterface::class, $meteringValue->getDate());

        $meteringData = $service->requestProductionLoadCurve(
            $token->getAccessToken(),
            $token->getUsagePointsId(),
            new \DateTimeImmutable('2 days ago midnight'),
            new \DateTimeImmutable('yesterday midnight')
        );

        self::assertInstanceOf(MeteringData::class, $meteringData);
        self::assertSame(MeteringData::TYPE_PRODUCTION_LOAD_CURVE, $meteringData->getDataType());
        self::assertGreaterThan(24, \count($meteringData->getValues()));
        self::assertSame('W', $meteringData->getUnit());

        $meteringValue = $meteringData->getValues()[0];
        self::assertEquals($meteringValue->getIntervalLength(), new \DateInterval('PT30M'));
        self::assertNotNull($meteringValue->getValue());
        self::assertInstanceOf(\DateTimeInterface::class, $meteringValue->getDate());
    }

    private function gettingCustomerData(MockCustomersService $service, Token $token): void
    {
        $address = $service->requestUsagePointAdresse(
            $token->getAccessToken(),
            $token->getUsagePointsId()
        );

        self::assertInstanceOf(Address::class, $address);
        self::assertSame($token->getUsagePointsId(), $address->getUsagePointId());
    }
}
