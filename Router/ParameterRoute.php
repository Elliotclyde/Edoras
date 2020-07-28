<?php

class ParameterRoute
{
    public function __construct($name,$arguments)
    {
        $this->name=$name;
        $this->route =$arguments[0];
        $this->method =$arguments[1];
        $this->parameterName = $this->getParameterName($this->route);
    }
    private function getParameterName($route)
    {
        $matches = [];
        preg_match('/(?<={)(.*)(?=})/', $route, $matches);
        return $matches[0];
    }
    private function getParameterValue($route)
    {
        $result = $route;
        $result = str_replace($this->getStart(), '', $result);
        $result = str_replace($this->geteEnd(), '', $result);
        return $result;
    }
    public function matches($route)
    {
        return (startsWith($route, $this->getStart()) && (endsWith($route, $this->geteEnd())));
    }
    public function getParameter($route){
        return [$this->parameterName => $this->getParameterValue($route)];
    }
    private function getStart()
    {
        return preg_replace('/{.*/', '', $this->route);
    }
    private function geteEnd()
    {
        return preg_replace('/([^-]*)}/', '', $this->route);
    }
}
