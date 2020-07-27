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
        if ($name === "view") {
            $this->createViewRoute($arguments);
            return;
        }
        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }
        if (preg_match('/{|}/', $arguments[0])) {
            $this->createParameterRoute($name, $arguments);
        }
        $this->createClosureRoute($name, $arguments);

    }
    private function createParameterRoute($name, $arguments)
    {
        $parameterRoute = new ParameterRoute($name, $arguments);
        
        if(!property_exists($this,'parameterRoutes')){
            $this->parameterRoutes=new stdClass();
        }
        if(!property_exists($this->parameterRoutes,strtolower($name))){
            $this->parameterRoutes->{strtolower($name)}=[];
        }
        
        array_push($this->parameterRoutes->{strtolower($name)},$parameterRoute);
    
    }


    private function createClosureRoute($name, $arguments)
    {
        list($route, $method) = $arguments;

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

    private function getParameterRoute($httpMethod, $route)
    {
        $parameterRoutes = $this->parameterRoutes->$httpMethod;
        foreach ($parameterRoutes as $current) {
            if ($current->matches($route)){
                return $current;
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
                $parameterRoute=$this->getParameterRoute($httpMethod, $formatedRoute);
            if ($parameterRoute) {
                $method = $parameterRoute->method;
                $parameters= $parameterRoute->getParameter($formatedRoute);
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
