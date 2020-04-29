<?php

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\Token;
use PHPUnit\Framework\TestCase;

final class TokenTest extends TestCase
{
    public function testHydratation()
    {
        $data = <<<JSON
{
    "access_token": "WeOAFUQA7KjyvWRujg6pqCNshq6pxJaC497Ubz3bku12lF4SW5Dws5",
    "token_type": "Bearer",
    "expires_in": 12600,
    "refresh_token": "Aq3WgspVeiUCCSxtsRBvr88GIRKAibcmYtBSPReLOPL2wA",
    "scope": "/v3/customers/usage_points/addresses.GET /v3/customers/usage_points/contracts.GET /v4/metering_data/daily_consumption.GET /v3/customers/contact_data.GET /v4/metering_data/consumption_max_power.GET /v4/metering_data/consumption_load_curve.GET /v3/customers/identity.GET",
    "refresh_token_issued_at": "1542279238976",
    "issued_at": "1542289239976",
    "usage_points_id": "12546852467895"
}
JSON;

        $token = Token::fromJson($data);

        self::assertInstanceOf(Token::class, $token);
        self::assertSame("WeOAFUQA7KjyvWRujg6pqCNshq6pxJaC497Ubz3bku12lF4SW5Dws5", $token->getAccessToken());
        self::assertEquals(\DateTimeImmutable::createFromFormat('U', (int) 1542289239.976), $token->getAccessTokenIssuedAt());
        self::assertSame("Aq3WgspVeiUCCSxtsRBvr88GIRKAibcmYtBSPReLOPL2wA", $token->getRefreshToken());
        self::assertEquals(\DateTimeImmutable::createFromFormat('U', (int) 1542279238.976), $token->getRefreshTokenIssuedAt());
        self::assertEquals("Bearer", $token->getTokenType());
        self::assertEquals("/v3/customers/usage_points/addresses.GET /v3/customers/usage_points/contracts.GET /v4/metering_data/daily_consumption.GET /v3/customers/contact_data.GET /v4/metering_data/consumption_max_power.GET /v4/metering_data/consumption_load_curve.GET /v3/customers/identity.GET", $token->getScope());
    }
}
