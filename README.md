# Enedis Data Connect API: a small library to use a part of Enedis Data Connect API with PHP

Firstly developped to be used in [aeneria](https://gitlab.com/aeneria/aeneria-app), this small library let you use easily
[Enedis Data Connect API](https://datahub-enedis.fr/data-connect/).

## Requirements

PHP 7.3 or higher

## Getting started

First you will need a ClientID and a ClientSecret to use Enedis Data Connect API.
Visit https://datahub-enedis.fr/data-connect, to know how to get one.

Then, install the library with composer:

```sh
composer require aeneria/enedis-data-connect
```

Now you are ready to start using it.

If you are using a framework such as Symfony, you can declare a service and use it with
dependency injection:

```yaml
# config/services.yaml

services:
    Aeneria\DataConnectService:
        class: Aeneria\DataConnectService
        arguments:
            $authEndpoint: "https://mon-compte-particulier.enedis.fr"
            $meteringDataEndpoint: "https://gw.prd.api.enedis.fr"
            $clientId: "YOUR_CLIENT_ID"
            $clientSecret: "YOUR_CLIENT_SECRET"
```

Or declare it in your code this way:

```php
<?php

use Aeneria\EnedisDataConnectApi\Services\DataConnectService;

//...

$dataConnect = new DataConnectService(
    "https://mon-compte-particulier.enedis.fr",
    "https://gw.prd.api.enedis.fr",
    "YOUR_CLIENT_ID",
    "YOUR_CLIENT_SECRET"
);

//...

```

Then you can use the API:

```php
<?php

// Getting consent page URL
$dataConnect->getConsentPageUrl('P6M', $state);

// Getting Tokens form Code
$token = $dataConnect->requestTokenFromCode($param['code']);

// Getting Tokens from Refresh Token
$token = $dataConnect->requestTokenFromRefreshToken($token->getRefreshToken());

// Getting consumption data
$meteringData = $dataConnect->requestDailyConsumption(
    $token->getUsagePointsId(),
    new \DateTimeImmutable('8 days ago'),
    new \DateTimeImmutable('yesterday'),
    $token->getAccessToken()
);
$meteringData = $dataConnect->requestConsumptionLoadCurve(
    $token->getUsagePointsId(),
    new \DateTimeImmutable('2 days ago'),
    new \DateTimeImmutable('yesterday'),
    $token->getAccessToken()
);

// Getting production data
$meteringData = $dataConnect->requestDailyProduction(
    $token->getUsagePointsId(),
    new \DateTimeImmutable('8 days ago'),
    new \DateTimeImmutable('yesterday'),
    $token->getAccessToken()
);
$meteringData = $dataConnect->requestProductionLoadCurve(
    $token->getUsagePointsId(),
    new \DateTimeImmutable('2 days ago'),
    new \DateTimeImmutable('yesterday'),
    $token->getAccessToken()
);

```

## Support

Feel free to [open an issue](https://gitlab.com/aeneria/enedis-data-connect/-/issues)!
