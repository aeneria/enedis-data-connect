<?php

namespace Aeneria\EnedisDataConnectApi\Service;

use Aeneria\EnedisDataConnectApi\Model\Token;

/**
 * Implements DataConnect AuthorizeV1 API
 *
 * @see https://datahub-enedis.fr/data-connect/documentation/authorize-v1/
 */
interface AuthorizeV1ServiceInterface
{
    /**
     * Get a URL to DataConnect consent page.
     *
     * @var string Durée du consentement demandé par l’application,
     * au format ISO 8601. Cette durée sera affichée au consommateur et ne peut
     * excéder 3 ans. (ex : P6M pour 6 mois)
     *
     * @var string Paramètre de sécurité permettant de maintenir l’état
     * entre la requête et la redirection.
     */
    public function getConsentPageUrl(string $duration, string $state): string;

    /**
     * Get DataConnectToken from a grant code.
     */
    public function requestTokenFromCode(string $code): Token;

    /**
     * Get DataConnectToken from a refreshToken.
     */
    public function requestTokenFromRefreshToken(string $refreshToken): Token;
}
