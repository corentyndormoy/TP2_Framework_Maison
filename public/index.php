<?php

use App\Kernel;

//require __DIR__ . "/../vendor/autoload.php";
//require(__DIR__."/../vendor/autoload.php");
require(__dir__."/../vendor/autoload.php");
//require __DIR__ . DIRECTORY_SEPARATOR . '../vendor/autoload.php';
//require(__DIR__ . DIRECTORY_SEPARATOR . '../vendor/autoload.php');

$kernel = new Kernel();
$kernel->run();