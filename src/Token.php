<?php

namespace Aeneria\EnedisDataConnectApi;

/**
 * A representation of a DataConnect Token received from Data Connect API
 *
 * {
 *   "access_token": "ba42fe5a-0eaa-11e5-9813-4dd05b3a25f3",
 *   "token_type": "Bearer",
 *   "expires_in": 12600,
 *   "refresh_token": "7dnCbf8P0ypCyxbnX7tUKjcSveE2Nu8w",
 *   "scope": "/v3/metering_data/consumption_load_curve.GET",
 *   "issued_at": "1487075532179",
 *   "refresh_token_issued_at": "1487075532179",
 *   "usage_points_id": "16401220101758,16401220101710,16401220101720"
 * }
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/authorize-v1/
 */
class Token
{
    /** @var string */
    private $accessToken;

    /** @var string */
    private $tokenType;

    /** @var string */
    private $scope;

    /** @var \DateTimeImmutable */
    private $accessTokenIssuedAt;

    /** @var string */
    private $refreshToken;

    /** @var \DateTimeImmutable */
    private $refreshTokenIssuedAt;

    /** @var string */
    private $usagePointsId;

    public static function fromJson(string $jsonData): self
    {
        $token = new self();

        try {
            $data = \json_decode($jsonData);

            $token->accessToken = $data->access_token;
            $token->accessTokenIssuedAt = \DateTimeImmutable::createFromFormat('U', (int) ($data->issued_at / 1000));
            $token->refreshToken = $data->refresh_token;
            $token->refreshTokenIssuedAt = \DateTimeImmutable::createFromFormat('U', (int) ($data->refresh_token_issued_at / 1000));
            $token->usagePointsId = $data->usage_points_id;
            $token->tokenType = $data->token_type;
            $token->scope = $data->scope;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(\sprintf(
                "La conversion vers l'objet Token a échoué : %s",
                $e->getMessage()
            ));
        }

        return $token;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getAccessTokenIssuedAt(): ?\DateTimeImmutable
    {
        return $this->accessTokenIssuedAt;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getRefreshTokenIssuedAt(): ?\DateTimeImmutable
    {
        return $this->refreshTokenIssuedAt;
    }

    public function getUsagePointsId(): ?string
    {
        return $this->usagePointsId;
    }
}
