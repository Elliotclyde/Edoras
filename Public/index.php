<?php

include_once __DIR__ . '/../Bootstrap.php';

$router->view('/', "home");
$router->view('/backend', 'backend');

$router->post('/backend', function () use ($requestVariables, $auth) {

    if ($auth->login($requestVariables['username'], $requestVariables['password'])) {
        header("Location: {$_SERVER["HTTP_ORIGIN"]}/backend/posts/");
    } else {return <<<HTML
    <h1>Sorry, you aren't logged in.</h1>
  HTML;
    }
});

$router->get('/backend/posts', function () use ($auth) {

    if (!$auth->check()) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    } else {
        $posts = (new Model('posts'))->selectAll();
        $postList = '';
        foreach ($posts as $post) {
            $postList .= (new View('backendpostitem', (array)$post))->make();
        }
        return (new View('backendpostlist', ['postList' => $postList]))->make();
    }
});

$router->get('/backend/posts/{postid}/edit', function ($postId) use ($auth) {
  if (!$auth->check()) {
      header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  } else {

    $posts = (new Model('posts'))->selectWhere('id', $postId);
    if (count($posts) == 0) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    } else {
        $currentPost = $posts[0];
        return (new View('backendpostupdate', (array)$currentPost))->make();
    }
  }
});

$router->post('/backend/posts/{postid}/edit', function ($postId) use ($auth, $requestVariables) {
  $posts = new Model('posts');
  if (!$auth->check() || !$posts->selectWhere('id',$postId) ) {
      header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  } else {

    foreach ($requestVariables as $key => $value){
      $posts->updateWhere([$key => $value],['id'=> $postId]);
    }
    header("Location: {$_SERVER["HTTP_ORIGIN"]}/backend/posts/");
  }
});


$router->get('/backend/posts/new', function () use ($auth) {
  if (!$auth->check()) {
      header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  } else {
    return (new View('backendpostnew'))->make(); 
  }
});

$router->post('/backend/posts/new', function () use ($auth, $requestVariables) {
  
  if (!$auth->check()) {
      header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  } else {
    $posts = new Model('posts');
    $id = $posts->new($requestVariables)[0];
    header("Location: {$_SERVER["HTTP_ORIGIN"]}/backend/posts/");
  }
});

$router->delete('/backend/posts/{id}', function ($id) use ($auth) {
  if (!$auth->check()) {
      header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  } else {
    $posts = new Model('posts');
    $posts->deleteWhere('id',$id);
    header("Location: {$_SERVER["HTTP_ORIGIN"]}/backend/posts/");
  }
});


$router->get('/posts', function (){

      $posts = (new Model('posts'))->selectAll();
      $postList = '';
      foreach ($posts as $post) {
          $postList .= (new View('postitem', (array)$post))->make();
      }
      return (new View('backendpostlist', ['postList' => $postList]))->make();
});

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

$router->get('/posts/{post}', function ($slug) {
    $posts = (new Model('posts'))->selectWhere('slug', $slug);
    if (count($posts) == 0) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    } else {
        $currentPost = $posts[0];
        return (new View('post', ['title' => $currentPost->title, 'body' => $currentPost->body]))->make();
    }
});
