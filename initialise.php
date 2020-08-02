<?php


// Set up env
$envKeyValPairs = explode(PHP_EOL,file_get_contents(__DIR__ . '/.env'));
foreach ($envKeyValPairs as $element){
    $keyValPair = explode('=',$element);
    $_ENV[$keyValPair[0]]=$keyValPair[1];
}

require __DIR__ . '/vendor/autoload.php';

include_once 'Router/Request.php';
include_once 'Router/Router.php';
include_once 'View/View.php';
include_once 'Model/Model.php';






