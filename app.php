<?php

require __DIR__.'/vendor/autoload.php';

use CalculateFeeBundle\Common\Main;
use GuzzleHttp\Client;

$inputData              = file_get_contents($argv[1]);
$client                 = new Client();
$binProvider            = new \CalculateFeeBundle\DataSource\BinProvider($client);
$exchangeRateProvider   = new \CalculateFeeBundle\DataSource\ExchangeRateProvider($client);
$commssionProvider      = new \CalculateFeeBundle\DataSource\CommissionProvider();

$app = new Main($inputData, $binProvider, $exchangeRateProvider, $commssionProvider);
$app->printInStdOut($argv[2]);