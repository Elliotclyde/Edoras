<?php

include_once __DIR__ . '/../Bootstrap.php';

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

$router->get('/posts/{post}',function($slug){
  $post = (new Model('posts'))->selectWhere('slug',$slug);
  if(count($post)==0){
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
  }
  else{
    return (new View('post',['title'=>$post[0]->title,'body'=>$post[0]->body]))->make();
  } 
});
$router->post('/',function() use ($requestVariables){
  return <<<HTML
  <h1>Gidday {$requestVariables['textinput']}, you're a BIG DAWG</h1>
HTML;
});