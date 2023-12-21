<?php

declare(strict_types=1);

namespace Aeneria\EnedisDataConnectApi\Model;

/**
 * A representation of a DataConnect Token received from Data Connect API
 *
 * {
 *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6Ik5HRTNaVEkwWVRnMk1HRm1ZVFppTWpCak16WXlNRE0wTkRrM1lXRXpNVGRtT0RSbU1UWmhNUT09In0.eyJhdWQiOiJodHRwOlwvXC9vcmcud3NvMi5hcGltZ3RcL2dhdGV3YXkiLCJzdWIiOiJhcGlzdWJzY3JpYmVyQGNhcmJvbi5zdXBlciIsImFwcGxpY2F0aW9uIjp7Im93bmVyIjoiYXBpc3Vic2NyaWJlciIsInRpZXJRdW90YVR5cGUiOiJyZXF1ZXN0Q291bnQiLCJ0aWVyIjoiVW5saW1pdGVkIiwibmFtZSI6IkRlZmF1bHRBcHBsaWNhdGlvbiIsImlkIjoxLCJ1dWlkIjpudWxsfSwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2VmdzljMHQxLjJremUwLnplMC5lcmQuZWRmLmZyOjgxODRcL29hdXRoMlwvdG9rZW4iLCJ0aWVySW5mbyI6eyJVbmxpbWl0ZWQiOnsidGllclF1b3RhVHlwZSI6InJlcXVlc3RDb3VudCIsInN0b3BPblF1b3RhUmVhY2giOnRydWUsInNwaWtlQXJyZXN0TGltaXQiOjAsInNwaWtlQXJyZXN0VW5pdCI6bnVsbH0sIkN1c3RvbSI6eyJ0aWVyUXVvdGFUeXBlIjoicmVxdWVzdENvdW50Iiwic3RvcE9uUXVvdGFSZWFjaCI6dHJ1ZSwic3Bpa2VBcnJlc3RMaW1pdCI6LTEsInNwaWtlQXJyZXN0VW5pdCI6Ik5BIn19LCJrZXl0eXBlIjoiUFJPRFVDVElPTiIsInN1YnNjcmliZWRBUElzIjpbeyJzdWJzY3JpYmVyVGVuYW50RG9tYWluIjoiY2FyYm9uLnN1cGVyIiwibmFtZSI6IlBpenphU2hhY2tBUEkiLCJjb250ZXh0IjoiXC9waXp6YXNoYWNrXC8xLjAuMCIsInB1Ymxpc2hlciI6ImFwaXB1Ymxpc2hlciIsInZlcnNpb24iOiIxLjAuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJVbmxpbWl0ZWQifSx7InN1YnNjcmliZXJUZW5hbnREb21haW4iOiJjYXJib24uc3VwZXIiLCJuYW1lIjoicmVzZWF1IiwiY29udGV4dCI6IlwvZWxlY3RyaWNfZ3JpZFwvdjNcLzMuMCIsInB1Ymxpc2hlciI6ImFwaXB1Ymxpc2hlciIsInZlcnNpb24iOiIzLjAiLCJzdWJzY3JpcHRpb25UaWVyIjoiVW5saW1pdGVkIn0seyJzdWJzY3JpYmVyVGVuYW50RG9tYWluIjoiY2FyYm9uLnN1cGVyIiwibmFtZSI6IlRvcGljQVBJIiwiY29udGV4dCI6IlwvVG9waWNTZXJ2aWNlXC8xLjAuMCIsInB1Ymxpc2hlciI6ImFwaXB1Ymxpc2hlciIsInZlcnNpb24iOiIxLjAuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJVbmxpbWl0ZWQifSx7InN1YnNjcmliZXJUZW5hbnREb21haW4iOiJjYXJib24uc3VwZXIiLCJuYW1lIjoic2VydmljZTFiaXMiLCJjb250ZXh0IjoiXC9zZXJ2aWNlMWJpc1wvMS4wLjAiLCJwdWJsaXNoZXIiOiJhcGlwdWJsaXNoZXIiLCJ2ZXJzaW9uIjoiMS4wLjAiLCJzdWJzY3JpcHRpb25UaWVyIjoiQ3VzdG9tIn1dLCJjb25zdW1lcktleSI6IlFPTE5PR3ZYTHQ5OE93X3B1aGZXbGZIeG1WQWEiLCJleHAiOjE2MzA2ODAyODYsImlhdCI6MTYzMDY3NjY4NiwianRpIjoiMWUwMWI1MzEtZGUyMS00YWM1LWI4MWItZTJhZDU3YWIzZjkxIn0.Q5bzRsI2JiPQA7KdJtnd_iSNBIOHQEyExHWGYFSVl-aK3KM8Fv1361hH7HlDUcp7ElGrc5v5ARS0j8-OqJiF8R_kJB7UOVn20VE3ZsYAzVxI3FXU6sEoBmZE7WHX1uwLAO1ClcuCw4zIDrckJgtpPxhWvN2LTd9ZKJVaXa2gxScHZM4Tc_HJqsPiqTIh6z_dNk0_al1P1b3_7hgP1bD6oysAz99dA7PF2w7WKOlfBPtHH89yfQA54XntLiRAdbl5t3KdFbn7r91R62iJ5i3kl_ogtwcoqwzK0ClAGev4OumPBFtaoNglgmaX8_rIG2-rhJwRqitiTeR_Kc5STtSXXA",
 *   "token_type": "Bearer",
 *   "expires_in": "12600",
 *   "scope": ""
 * }
 *
 * @see https://datahub-enedis.fr/services-api/data-connect/documentation/jeton/
 */
class Token
{
    public string $accessToken;
    public string $tokenType;
    public string $scope;
    public \DateTimeImmutable $accessTokenExpirationDate;
    public string $rawData;

    public static function fromJson(string $jsonData): self
    {
        $token = new self();
        $token->rawData = $jsonData;

        try {
            $data = \json_decode($jsonData);

            $token->accessToken = $data->access_token;
            $expirationDate = (new \DateTime())->add(new \DateInterval('PT' . $data->expires_in . 'S'));
            $token->accessTokenExpirationDate = \DateTimeImmutable::createFromMutable($expirationDate);
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

    public function isAccessTokenStillValid(): bool
    {
        return $this->accessTokenExpirationDate && ($this->accessTokenExpirationDate > new \DateTimeImmutable());
    }
}
