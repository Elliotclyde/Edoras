<?php

include_once __DIR__ . '/../Bootstrap.php';

$router->view('/', "home");
$router->view('/backend', 'backend');

$router->get('/cont/{dawg}',"PostController@show");

$router->post('/backend', function () use ($requestVariables, $auth) {
    if ($auth->login($requestVariables['username'], $requestVariables['password'])) {
        header("Location: {$_SERVER["HTTP_ORIGIN"]}/backend/posts/");
    } else {return <<<HTML
    <h1>Sorry, you aren't logged in.</h1>
  HTML;
    }
});

$router->get('/backend/posts',"PostController@backendIndex");
$router->get('/backend/posts/{postid}/edit',"PostController@edit");
$router->post('/backend/posts/{postid}/edit',"PostController@update");
$router->get('/backend/posts/new',"PostController@create");
$router->post('/backend/posts/new',"PostController@store");
$router->get('/posts',"PostController@index");
$router->get('/posts/{post}',"PostController@show");
$router->delete('/backend/posts/{id}', "PostController@destroy");


$router->post('/data', function ($request) {
    return json_encode($request);
});

$router->get('/flora', function () {
    return <<<HTML
  <h1>purrrr ... purrr ... purrrr ... ZZZZZzzzzz</h1>
HTML;
});

$router->view('/profile', "profile", ["title" => "Yo yo yo"]);

$router->view('/liz', 'person', ['person' => 'Elizabeth', 'catchphrase' => 'Cowabunga dude']);

$router->get('/hugh', function () {
    return (new View('person', ['person' => 'Hugh', 'catchphrase' => 'I am a person']))->make();
});

$router->view('/eve', 'person', ['person' => 'Eve', 'catchphrase' => 'I got married and it was so fun!']);

$router->get('/durry/{durrynum}', function ($durries) {
    return
        "<h1>Durries mate</h1>" .
        "<p>You've got {$durries} durries.</p>";
});

$router->get('/bigdawg/{name}/yes', function ($name) {
    $capitalised = ucwords($name);
    return <<<HTML
  <h1>Gidday {$capitalised}, you're a BIG DAWG</h1>
HTML;
});

