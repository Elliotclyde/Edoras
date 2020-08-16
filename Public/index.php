<?php

/*
* Define your routes here
* You can make closure routes, parameter routes, view routes . . . 
* Take a look at the docs for what you can do!
*/


include_once __DIR__ . '/../Bootstrap.php';

$router->get('/', "HomeController@index");