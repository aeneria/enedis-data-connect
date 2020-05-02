<?php

namespace Aeneria\EnedisDataConnectApi\Tests\Functional\Services;

use Aeneria\EnedisDataConnectApi\Model\Address;
use Aeneria\EnedisDataConnectApi\Model\MeteringData;
use Aeneria\EnedisDataConnectApi\Service\AuthorizeV1Service;
use Aeneria\EnedisDataConnectApi\Service\DataConnectService;
use Aeneria\EnedisDataConnectApi\Service\MeteringDataV4Service;
use Aeneria\EnedisDataConnectApi\Model\Token;
use Aeneria\EnedisDataConnectApi\Service\CustomersService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

final class DataConnectServiceTest extends TestCase
{
    /**
     * That's a bit ugly but we test all DataConnectService methods in a single test
     * to avoid calling Enedis API to many times.
     */
    public function testDataConnectService()
    {
        $dataConnect = new DataConnectService(
            "https://gw.hml.api.enedis.fr",
            "https://gw.hml.api.enedis.fr",
            $_ENV["CLIENT_ID"],
            $_ENV["CLIENT_SECRET"],
            $_ENV["REDIRECT_URI"],
        );

        // Test Authorize V1 API
        $token = $this->gettingConsent($dataConnect->getAuthorizeV1Service());
        $token = $this->gettingAccessToken($dataConnect->getAuthorizeV1Service(), $token);

        // Test Metering Data V4 API
        $this->gettingConsumptionData($dataConnect->getMeteringDataV4Service(), $token);
        // My client Id currently can't get production data, it's outside my authorized scope !
        // $this->gettingProductionData($dataConnect->getMeteringDataV4Service(), $token);

        // Test Customers API
        // $this->gettingCustomerData($dataConnect->getCustomersService(), $token);
    }

    private function gettingConsent(AuthorizeV1Service $service): Token
    {
        $response = HttpClient::create()->request(
            'GET',
            $service->getConsentPageUrl('P6M', $state = \md5(\uniqid(\rand(), true)) . '0')
        );

        // Parsing response to find redirect URL in it
        foreach (\preg_split("/((\r?\n)|(\r\n?))/", $response->getContent()) as $line) {
            if ($substr = \strstr($line, "var url =")) {
                $substr = \ltrim($substr, 'var url = "');
                $url = \rtrim($substr, '";');
            }
        }
        \parse_str(\parse_url($url, \PHP_URL_QUERY), $param);
        self::assertArrayHasKey('code', $param);
        self::assertArrayHasKey('usage_point_id', $param);

        $token = $service->requestTokenFromCode($param['code']);

        self::assertInstanceOf(Token::class, $token);
        self::assertSame($param['usage_point_id'], $token->getUsagePointsId());
        self::assertNotNull($token->getRefreshToken());

        return $token;
    }

    private function gettingAccessToken(AuthorizeV1Service $service, Token $token): Token
    {
        $token = $service->requestTokenFromRefreshToken($token->getRefreshToken());

        self::assertInstanceOf(Token::class, $token);
        self::assertNotNull($token->getRefreshToken());
        self::assertNotNull($token->getAccessToken());

        return $token;
    }

    private function gettingConsumptionData(MeteringDataV4Service $service, Token $token): void
    {
        $meteringData = $service->requestDailyConsumption(
            $token->getAccessToken(),
            $token->getUsagePointsId(),
            new \DateTimeImmutable('8 days ago'),
            new \DateTimeImmutable('yesterday')
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
            new \DateTimeImmutable('2 days ago'),
            new \DateTimeImmutable('yesterday')
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

    private function gettingProductionData(MeteringDataV4Service $service, Token $token): void
    {
        $meteringData = $service->requestDailyProduction(
            $token->getAccessToken(),
            $token->getUsagePointsId(),
            new \DateTimeImmutable('8 days ago'),
            new \DateTimeImmutable('yesterday')
        );

        self::assertInstanceOf(MeteringData::class, $meteringData);
        self::assertSame(MeteringData::TYPE_DAILY_CONSUMPTION, $meteringData->getDataType());
        self::assertSame(7, \count($meteringData->getValues()));
        self::assertSame('Wh', $meteringData->getUnit());

        $meteringValue = $meteringData->getValues()[0];
        self::assertEquals(new \DateInterval('P1D'), $meteringValue->getIntervalLength());
        self::assertNotNull($meteringValue->getValue());
        self::assertInstanceOf(\DateTimeInterface::class, $meteringValue->getDate());

        $meteringData = $service->requestProductionLoadCurve(
            $token->getAccessToken(),
            $token->getUsagePointsId(),
            new \DateTimeImmutable('2 days ago'),
            new \DateTimeImmutable('yesterday')
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

    private function gettingCustomerData(CustomersService $service, Token $token): void
    {
        $address = $service->requestUsagePointAdresse(
            $token->getAccessToken(),
            $token->getUsagePointsId()
        );

        self::assertInstanceOf(Address::class, $address);
        self::assertSame($token->getUsagePointsId(), $address->getUsagePointId());
    }
}
