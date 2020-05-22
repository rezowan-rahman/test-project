<?php

use CalculateFeeBundle\Common\Main;

require __DIR__.'/vendor/autoload.php';

$inputData = file_get_contents($argv[1]);
$app = new Main($inputData);
$app->printInStdOut($round = true);