<?php

require __DIR__.'/vendor/autoload.php';

use CalculateFeeBundle\Common\Main;

$inputData = file_get_contents($argv[1]);
$data = new \CalculateFeeBundle\DataSource\Data();
$provider = new \CalculateFeeBundle\DataSource\Provider();

$app = new Main($inputData, $data, $provider);
$app->printInStdOut($argv[2]);