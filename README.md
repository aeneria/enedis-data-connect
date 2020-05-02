# Enedis Data Connect API: a small library to use a part of Enedis Data Connect API with PHP

Firstly developped to be used in [aeneria](https://gitlab.com/aeneria/aeneria-app), this small library let you use easily
[Enedis Data Connect API](https://datahub-enedis.fr/data-connect/).

## Requirements

PHP 7.3 or higher

## Getting started

First of all, you will need a ClientID and a ClientSecret to use Enedis Data Connect API.
Visit https://datahub-enedis.fr/data-connect, to know how to get ones. During your
registration you will also give a redirect URL, you will need it touse this library.

Now that you have those, install the library with composer:

```sh
composer require aeneria/enedis-data-connect
```

If you are using a framework such as Symfony, you can declare the library as a service and
use it with dependency injection:

```yaml
# config/services.yaml

services:
    Aeneria\EnedisDataConnectApi\Service\DataConnectService:
        class: Aeneria\EnedisDataConnectApi\Service\DataConnectService
        arguments:
            $authEndpoint: "https://mon-compte-particulier.enedis.fr"
            $dataEndpoint: "https://gw.prd.api.enedis.fr"
            $clientId: "YOUR_CLIENT_ID"
            $clientSecret: "YOUR_CLIENT_SECRET"
            $redirectUri: "YOUR_REDIRECT_URI"
```

Or you can declare it in your code this way:

```php
<?php

use Aeneria\EnedisDataConnectApi\Services\DataConnectService;

//...

$dataConnect = new DataConnectService(
    "https://mon-compte-particulier.enedis.fr",
    "https://gw.prd.api.enedis.fr",
    "YOUR_CLIENT_ID",
    "YOUR_CLIENT_SECRET",
    "YOUR_REDIRECT_URI
);

//...

```

Then you can use the API:

```php
<?php

// Getting consent page URL
$dataConnect->getAuthorizeV1Service()->getConsentPageUrl('P6M', $state);

// Getting Tokens form Code
$token = $dataConnect->getAuthorizeV1Service()->requestTokenFromCode($param['code']);

// Getting Tokens from Refresh Token
$token = $dataConnect->getAuthorizeV1Service()->requestTokenFromRefreshToken($token->getRefreshToken());

// Getting consumption data
$meteringData = $dataConnect->getMeteringDataV4Service()->requestDailyConsumption(
    $token->getAccessToken(),
    $token->getUsagePointsId(),
    new \DateTimeImmutable('8 days ago'),
    new \DateTimeImmutable('yesterday')
);
$meteringData = $dataConnect->getMeteringDataV4Service()->requestConsumptionLoadCurve(
    $token->getAccessToken(),
    $token->getUsagePointsId(),
    new \DateTimeImmutable('2 days ago'),
    new \DateTimeImmutable('yesterday')
);

// Getting production data
$meteringData = $dataConnect->getMeteringDataV4Service()->requestDailyProduction(
    $token->getAccessToken(),
    $token->getUsagePointsId(),
    new \DateTimeImmutable('8 days ago'),
    new \DateTimeImmutable('yesterday')
);
$meteringData = $dataConnect->getMeteringDataV4Service()->requestProductionLoadCurve(
    $token->getAccessToken(),
    $token->getUsagePointsId(),
    new \DateTimeImmutable('2 days ago'),
    new \DateTimeImmutable('yesterday')
);

// Getting customer data
$address = $dataConnect->getCustomersService()->requestUsagePointAdresse(
    $token->getAccessToken(),
    $token->getUsagePointsId()
);

```

## Support

Feel free to [open an issue](https://gitlab.com/aeneria/enedis-data-connect/-/issues)!
