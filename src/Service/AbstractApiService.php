<?php

namespace Aeneria\EnedisDataConnectApi\Service;

use Aeneria\EnedisDataConnectApi\Exception\DataConnectConsentException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectDataNotFoundException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectException;
use Aeneria\EnedisDataConnectApi\Exception\DataConnectQuotaExceededException;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApiService
{
    protected function checkResponse(ResponseInterface $response): void
    {
        $code = $response->getStatusCode();

        if (200 !== $code) {
            switch ($code) {
                case 403:
                    throw new DataConnectConsentException($response->getContent(false), $code);
                case 404:
                    throw new DataConnectDataNotFoundException($response->getContent(false), $code);
                case 429:
                    throw new DataConnectQuotaExceededException($response->getContent(false), $code);
                default:
                    throw new DataConnectException($response->getContent(false), $code);
            }
        }
    }
}
