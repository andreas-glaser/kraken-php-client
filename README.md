#Kraken PHP Client
An efficient 

##Requirements
* PHP 7.*
* [Composer Dependency Manager](https://getcomposer.org/)
* [Kraken](https://poloniex.com/) account (optional but required for trading)

##Installation
```shell
composer require andreas-glaser/kpc
```

##Usage
```php
<?php

use AndreasGlaser\KPC\KPC;

$apiKey = 'YOUR_PRIVATE_API_KEY';
$apiSecret = 'YOUR_PRIVATE_API_SECRET';

/** @var KPC $pcc */
$pcc = new KPC($apiKey,$apiSecret);

$result = $pcc->buy('BTC_ETH', 0.034, 100, 1);

var_dump($result->decoded);