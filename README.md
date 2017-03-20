# Kraken PHP Client
An efficient kraken rest api client, built on top of [guzzle](https://github.com/guzzle/guzzle)

## Requirements
* PHP 7.x
* [Composer Dependency Manager](https://getcomposer.org/)
* [Kraken](https://kraken.com/) account (optional, but required for trading)

## Installation
```shell
composer require andreas-glaser/kraken-php-client
```

## Usage
```php
<?php

use AndreasGlaser\KPC\KPC;

$apiKey = 'YOUR_PRIVATE_API_KEY';
$apiSecret = 'YOUR_PRIVATE_API_SECRET';

/** @var KPC $kpc */
$kpc = new KPC($apiKey,$apiSecret);

$result = $kpc->getTicker(['XBTUSD', 'ETHXBT']);

var_dump($result->decoded);