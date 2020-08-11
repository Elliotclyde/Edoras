<?php

include_once "ParameterRoute.php";
include_once "ControllerRoute.php";
include_once __DIR__ . "/../View/View.php";

class Router
{
    private $request;
    private $called = false;

    private $supportedHttpMethods = array("GET", "POST","DELETE");

    public function __construct(IRequest $request)
    {
        $this->request = $request;
    }

    public function __call($name, $arguments)
    {
        $this->called = true;
        if ($name === "view") {
            $this->createViewRoute($arguments);
            return;
        }
        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }
        if(count($arguments)> 1 && is_string($arguments[1]) && substr_count($arguments[1],"@")==1){
            echo "Creating Controller Route";
            $this->createControllerRoute($name, $arguments);
        }
        if (preg_match('/{[a-zA-Z0-9]+}/', $arguments[0])) {
            $this->createParameterRoute($name, $arguments);
        }
        $this->createClosureRoute($name, $arguments);

    }

    //closure route

    private function createClosureRoute($name, $arguments)
    {
        list($route, $method) = $arguments;
        $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
    }

    //parameter route

    private function createParameterRoute($name, $arguments)
    {
        $parameterRoute = new ParameterRoute($name, $arguments);
        $this->generateParameterRouteProperty($name);

        array_push($this->parameterRoutes->{strtolower($name)}, $parameterRoute);
    }

    private function generateParameterRouteProperty($name)
    {

        if (!property_exists($this, 'parameterRoutes')) {
            $this->parameterRoutes = new stdClass();
        }
        if (!property_exists($this->parameterRoutes, strtolower($name))) {
            $this->parameterRoutes->{strtolower($name)} = [];
        }
    }

    //view route - always uses get http method

    private function createViewRoute($arguments)
    {
        list($viewRoute, $viewName, $viewArguments) = array_pad($arguments, 3, []);

        $this->{'get'}[$this->formatRoute($viewRoute)] =
        function () use ($viewName, $viewArguments) {
            return (new View($viewName, $viewArguments))->make();
        };
    }

    //Controller route

    private function createControllerRoute($name, $arguments)
    {
        $controllerRoute = new ControllerRoute($name, $arguments);
        $this->generateControllerRouteProperty($name);

        array_push($this->controllerRoutes->{strtolower($name)}, $controllerRoute);
    }

    private function generateControllerRouteProperty($name)
    {
        if (!property_exists($this, 'controllerRoutes')) {
            $this->controllerRoutes = new stdClass();
        }
        if (!property_exists($this->controllerRoutes, strtolower($name))) {
            $this->controllerRoutes->{strtolower($name)} = [];
        }
    }

    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if ($result === '') {
            return '/';
        }
        return $result;
    }

    private function invalidMethodHandler()
    {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function defaultRequestHandler()
    {
        header("{$this->request->serverProtocol} 404 Not Found");
    }

    private function getParameterRoute($httpMethod, $route)
     {
        
         if (!property_exists($this->parameterRoutes,$httpMethod)) {
             return false;
         } else {
            $parameterRoutes = $this->parameterRoutes->$httpMethod;
            foreach ($parameterRoutes as $current) {
                if ($current->matches($route)) {
                    return $current;
                }
            }
            return false;
        }
    }

    public function resolve()
    {
        // method dictionary - keys are the urls, values are method to call

        $httpMethod = strtolower($this->request->requestMethod);
        $methodDictionary = $this->$httpMethod;
        $formatedRoute = $this->formatRoute($this->request->requestUri);
        $parameters = [];
        
        //refactor this section

        if (!in_array($formatedRoute, array_keys($methodDictionary))) {
            $parameterRoute = $this->getParameterRoute($httpMethod, $formatedRoute);
            if ($parameterRoute) {
                $method = $parameterRoute->method;
                $parameters = $parameterRoute->getParameter($formatedRoute);
            } else {
                $this->defaultRequestHandler();
                return;
            }
        } else {
            $method = $methodDictionary[$formatedRoute];
        }
        
        echo call_user_func_array($method, $parameters);
    }

    public function __destruct()
    {
        if ($this->called) {
            $this->resolve();
        }
    }

}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}
