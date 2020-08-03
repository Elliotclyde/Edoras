<?php

include_once __DIR__ ."/../initialise.php";
include_once __DIR__ ."/../Model/Model.php";

$User = new Model('users');


print_r($User->getKeys());
print_r(var_dump($argv));

