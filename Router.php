<?php

class Router
{
    private $request;

    private $supportedHttpMethods = array("GET", "POST");

    public function __construct(IRequest $request)
    {
        $this->request = $request;
    }

    public function __call($name, $arguments)
    {
        if (preg_match('/{|}/', $arguments[0])) {
            $this->createParameterRoute($name, $arguments);
        }
        if ($name === "view") {
            $this->createViewRoute($arguments);
            return;
        }

        $this->createClosureRoute($name, $arguments);

    }
    private function createParameterRoute($name, $arguments)
    {
        list($route, $method) = $arguments;
        $currentRoute = $this->formatRoute($this->request->requestUri);

        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }
       
        if(! array_key_exists("parameterRoutes",$this->{strtolower($name)})){
            $this->{strtolower($name)}["parameterRoutes"] = [];
        }
        array_push($this->{strtolower($name)}["parameterRoutes"],[$this->formatRoute($route) => $method]);
     
    }

    private function checkForParameterRoute($currentRoute, $parameterRoute)
    {
        return (startsWith($currentRoute, $this->getParameterRouteStart($parameterRoute)) && (endsWith($currentRoute, $this->getParameterRouteEnd($parameterRoute))));
    }

    private function getParameterRouteStart($route)
    {
        return preg_replace('/{.*/', '', $route);
    }
    private function getParameterRouteEnd($route)
    {
        return preg_replace('/([^-]*)}/', '', $route);
    }
    private function getParameterRouteParameterName($route)
    {
        $matches = [];
        preg_match('/(?<={)(.*)(?=})/', $route, $matches);
        return $matches[0];
    }
    private function getParameterValue($currentRoute, $matchedRoute)
    {
        $result = $currentRoute;
        $result = str_replace($this->getParameterRouteStart($matchedRoute), '', $result);
        $result = str_replace($this->getParameterRouteEnd($matchedRoute), '', $result);

        return $result;
    }

    private function createClosureRoute($name, $arguments)
    {
        list($route, $method) = $arguments;

        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }
        $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
    }

    private function createViewRoute($arguments)
    {
        list($route, $viewName, $variables) = array_pad($arguments, 3, []);
        $viewContents = $this->getViewContents($viewName, $variables);

        $this->{'get'}[$this->formatRoute($route)] =
        function () use ($viewContents) {
            return $viewContents;
        };
    }

    private function getViewContents($viewName, $variables = array())
    {
        $ViewFilePathMatches = $this->getMatchingViewFiles($viewName);

        if (count($ViewFilePathMatches) === 0) {
            throw new Exception('no matching view filenames in views directory for ' . $viewName);
        }

        $filePath = "./views/" . reset($ViewFilePathMatches);
        $fileType = explode('.', reset($ViewFilePathMatches))[1];

        // if php return php output
        if ($fileType == 'php') {
            return $this->getPHPViewOutput($filePath, $variables);
        }

        // if non php file return
        return (file_get_contents($filePath));
    }

    private function getMatchingViewFiles($viewName)
    {
        return array_filter(
            scandir('./views'),
            function ($filePath) use ($viewName) {
                return (explode('.', $filePath)[0] === $viewName);
            });
    }

    private function getPHPViewOutput($filePath, $variables)
    {
        $output = null;
        if (file_exists($filePath)) {
            extract($variables);
            ob_start();
            include $filePath;
            $output = ob_get_clean();
        }
        return $output;
    }

    /**
     * Removes trailing forward slashes from the right of the route.
     * @param route (string)
     */
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

    private function getParameterRouteMethod($method, $route)
    {
        $parameterRoutes = $this->$method["parameterRoutes"];
        foreach ($parameterRoutes as $current) {
            if ($this->checkForParameterRoute($route, array_keys($current)[0])) {
                return $current[array_keys($current)[0]];
            }
        }
        return false;
    }
    private function getParameterRouteParameter($httpMethod, $route)
    {
        $parameterRoutes = $this->$httpMethod["parameterRoutes"];
        foreach ($parameterRoutes as $current) {
            if ($this->checkForParameterRoute($route, array_keys($current)[0])) {
                return [$this->getParameterRouteParameterName(array_keys($current)[0])=>$this->getParameterValue($route,array_keys($current)[0])];
            }
        }
        return false;
    }


    public function resolve()
    {
        // method dictionary - keys are the urls, values are method to call.
        // It is fetched by grabbing an index
        $httpMethod = strtolower($this->request->requestMethod);
        $methodDictionary = $this->$httpMethod;
        $formatedRoute = $this->formatRoute($this->request->requestUri);
        $parameters=[];
        if (!in_array($formatedRoute, array_keys($methodDictionary))) {
            if ($this->getParameterRouteMethod($httpMethod, $formatedRoute)) {
                $method = $this->getParameterRouteMethod($httpMethod, $formatedRoute);
                $parameters= $this->getParameterRouteParameter($httpMethod, $formatedRoute);
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
        $this->resolve();
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

class ParameterRoute
{
    public function __construct()
    {

    }
}
