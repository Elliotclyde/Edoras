<?php

/*
* Simple example of a controller. 
* These can be named whatever you want, and do not need to extend another class
* Although Pascal case is the convention, and could be a good idea
*/

class HomeController
{
    public function index()
    {
        return (new View('home',["greeting"=>"Welcome to Edoras"]))->make();
    }
}
