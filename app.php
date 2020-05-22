<?php

require __DIR__.'/vendor/autoload.php';

use CalculateFeeBundle\Common\Main;

$inputData = file_get_contents($argv[1]);
$app = new Main($inputData);
$app->printInStdOut($round = true);