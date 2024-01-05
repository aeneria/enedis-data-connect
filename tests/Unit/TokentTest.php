<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Tests\Unit;

use Aeneria\EnedisDataConnectApi\Model\Token;
use PHPUnit\Framework\TestCase;

final class TokenTest extends TestCase
{
    public function testHydratation()
    {
        $data = <<<JSON
        {
            "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6Ik5HRTNaVEkwWVRnMk1HRm1ZVFppTWpCak16WXlNRE0wTkRrM1lXRXpNVGRtT0RSbU1UWmhNUT09In0",
            "token_type": "Bearer",
            "expires_in": "12600",
            "scope": ""
        }
        JSON;

        $token = Token::fromJson($data);

        self::assertInstanceOf(Token::class, $token);
        self::assertSame("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6Ik5HRTNaVEkwWVRnMk1HRm1ZVFppTWpCak16WXlNRE0wTkRrM1lXRXpNVGRtT0RSbU1UWmhNUT09In0", $token->accessToken);
        self::assertTrue($token->isAccessTokenStillValid());
    }
}
