<?php

require __DIR__ . '/vendor/autoload.php';
include_once 'Request.php';
include_once 'Router/Router.php';
include_once 'View/View.php';

$router = new Router(new Request($_SERVER));

$router->view('/', "home");

$router->post('/data', function ($request) {
    return json_encode($request->getBody());
});

$router->get('/flora', function () {
    return <<<HTML
  <h1>purrrr ... purrr ... purrrr ... ZZZZZzzzzz</h1>
HTML;
});


$router->view('/profile', "profile",["title"=>"Yo yo yo"]);

$router->view('/liz','person',['person'=>'Elizabeth','catchphrase'=>'Cowabunga dude']);

$router->get('/hugh',function (){
    return (new View('person',['person'=>'Hugh','catchphrase'=>'I am a person']))->make();
});

$router->view('/eve','person',['person'=>'Eve','catchphrase'=>'I got married and it was so fun!']);

$router->get('/durry/{durrynum}',function($durries){
   return
  "<h1>Durries mate</h1>" .
   "<p>You've got {$durries} durries.</p>" ;
});

$router->get('/bigdawg/{name}/yes',function($name){
  $capitalised = ucwords($name);
  return <<<HTML
  <h1>Gidday {$capitalised}, you're a BIG DAWG</h1>
HTML;
});
