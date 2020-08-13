<?php

class Controller
{
    private $controllerInstance;
    public $controllerMethod;

    public function __construct($nameMethodString)
    {
        list($controllerName, $method) = explode('@',$nameMethodString);

        include_once(__DIR__ . "/../../Controllers/".$controllerName.".php");
        $class = $controllerName;
        $this->controllerInstance = new $class();
        $this->controllerMethod = array($this->controllerInstance, $method);
    }
    public function getMethod()
    {
       return $this->controllerMethod;
    }
}