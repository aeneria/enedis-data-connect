<?php

namespace Aeneria\EnedisDataConnectApi\Tests\Functional\Services;

use Aeneria\EnedisDataConnectApi\MeteringData;
use Aeneria\EnedisDataConnectApi\Services\DataConnectService;
use Aeneria\EnedisDataConnectApi\Token;
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
            $_ENV["CLIENT_SECRET"]
        );

        $token = $this->gettingConsent($dataConnect);
        $token = $this->gettingAccessToken($dataConnect, $token);
        $this->gettingConsumptionData($dataConnect, $token);
        $this->gettingProductionData($dataConnect, $token);
    }

    private function gettingConsent(DataConnectService $dataConnect): Token
    {
        $response = HttpClient::create()->request(
            'GET',
            $dataConnect->getConsentPageUrl('P6M', $state = \md5(\uniqid(\rand(), true)) . '0')
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

        $token = $dataConnect->requestDataConnectTokensFromCode($param['code']);

        self::assertInstanceOf(Token::class, $token);
        self::assertSame($param['usage_point_id'], $token->getUsagePointsId());
        self::assertNotNull($token->getRefreshToken());

        return $token;
    }

    private function gettingAccessToken(DataConnectService $dataConnect, Token $token): Token
    {
        $token = $dataConnect->requestDataConnectTokensFromRefreshToken($token->getRefreshToken());

        self::assertInstanceOf(Token::class, $token);
        self::assertNotNull($token->getRefreshToken());
        self::assertNotNull($token->getAccessToken());

        return $token;
    }

    private function gettingConsumptionData(DataConnectService $dataConnect, Token $token): void
    {
        $meteringData = $dataConnect->requestDailyConsumption(
            $token->getUsagePointsId(),
            new \DateTimeImmutable('8 days ago'),
            new \DateTimeImmutable('yesterday'),
            $token->getAccessToken()
        );

        self::assertInstanceOf(MeteringData::class, $meteringData);
        self::assertSame(MeteringData::TYPE_DAILY_CONSUMPTION, $meteringData->getDataType());
        self::assertSame(7, \count($meteringData->getValues()));
        self::assertSame('Wh', $meteringData->getUnit());

        $meteringValue = $meteringData->getValues()[0];
        self::assertEquals(new \DateInterval('P1D'), $meteringValue->getIntervalLength());
        self::assertNotNull($meteringValue->getValue());
        self::assertInstanceOf(\DateTimeInterface::class, $meteringValue->getDate());

        $meteringData = $dataConnect->requestConsumptionLoadCurve(
            $token->getUsagePointsId(),
            new \DateTimeImmutable('2 days ago'),
            new \DateTimeImmutable('yesterday'),
            $token->getAccessToken()
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

    private function gettingProductionData(DataConnectService $dataConnect, Token $token): void
    {
        $meteringData = $dataConnect->requestDailyProduction(
            $token->getUsagePointsId(),
            new \DateTimeImmutable('8 days ago'),
            new \DateTimeImmutable('yesterday'),
            $token->getAccessToken()
        );

        self::assertInstanceOf(MeteringData::class, $meteringData);
        self::assertSame(MeteringData::TYPE_DAILY_CONSUMPTION, $meteringData->getDataType());
        self::assertSame(7, \count($meteringData->getValues()));
        self::assertSame('Wh', $meteringData->getUnit());

        $meteringValue = $meteringData->getValues()[0];
        self::assertEquals(new \DateInterval('P1D'), $meteringValue->getIntervalLength());
        self::assertNotNull($meteringValue->getValue());
        self::assertInstanceOf(\DateTimeInterface::class, $meteringValue->getDate());

        $meteringData = $dataConnect->requestProductionLoadCurve(
            $token->getUsagePointsId(),
            new \DateTimeImmutable('2 days ago'),
            new \DateTimeImmutable('yesterday'),
            $token->getAccessToken()
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
}
