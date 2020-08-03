<?php

include_once 'Router/Router.php';
include_once 'Router/Request.php';

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRouterCanBeMadeAndCanMakeClosureRoute()
    {
        $router = new Router($this->getHomeMockRequest());
        $this->assertInstanceOf(Router::class, ($router));
        $router->get('/', function () {return 'hi';});
    }
    public function testRouterCanMakeViewRoute()
    {
        $router = new Router($this->getHomeMockRequest());
        $this->assertInstanceOf(Router::class, ($router));
        $router->view('/', 'home');
    }
    public function testRouterCanMakeVariableRoute()
    {
        $router = new Router($this->getVariableMockRequest());
        $this->assertInstanceOf(Router::class, ($router));
        $router->get('/dog/{dog}', function ($dog) {return 'you have 70 dogs';});
    }
    public function testRouterCanReceivePostHomeRoute()
    {
        $request = $this->getPostHomeMockRequest();
        $router = new Router($request);
        $this->assertInstanceOf(Router::class, ($router));
        $router->post('/', function ($i=1) {return "hi";});
    }



    
    private function getHomeMockRequest()
    {
        $str = file_get_contents('Tests/Router/homerequestmock.json');
        $json = json_decode($str, true); // decode the JSON into an associative arr
        return new Request($json);
    }
    private function getPostHomeMockRequest()
    {
        $str = file_get_contents('Tests/Router/posttohomerequestmock.json');
        $json = json_decode($str, true); // decode the JSON into an associative arr
        return new Request($json);
    }
    private function getVariableMockRequest()
    {
        $str = file_get_contents('Tests/Router/variablerequestmock.json');
        $json = json_decode($str, true); // decode the JSON into an associative arr
        return new Request($json);
    }

}
