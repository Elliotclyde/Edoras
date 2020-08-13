<?php

include_once __DIR__ . "/../Bootstrap/Controller/Controller.php";

class PostController
{
    public function index()
    {
        $posts = (new Model('posts'))->selectAll();
        $postList = '';
        foreach ($posts as $post) {
            $postList .= (new View('postitem', (array) $post))->make();
        }
        return (new View('backendpostlist', ['postList' => $postList]))->make();
    }

    public function backendIndex()
    {
        if (!(new Auth($_SESSION))->check()) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        } else {
            $posts = (new Model('posts'))->selectAll();
            $postList = '';
            foreach ($posts as $post) {
                $postList .= (new View('backendpostitem', (array) $post))->make();
            }
            return (new View('backendpostlist', ['postList' => $postList]))->make();
        }
    }

    public function create()
    {
        if (!(new Auth($_SESSION))->check()) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        } else {
            return (new View('backendpostnew'))->make();
        }
    }
    public function store()
    {
        if (!(new Auth($_SESSION))->check()) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        } else {
            $posts = new Model('posts');
            $id = $posts->new($_REQUEST)[0];
            header("Location: {$_SERVER["HTTP_ORIGIN"]}/backend/posts/");
        }
    }

    public function show($slug)
    {
        $posts = (new Model('posts'))->selectWhere('slug', $slug);
        if (count($posts) == 0) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        } else {
            $currentPost = $posts[0];
            return (new View('post', ['title' => $currentPost->title, 'body' => $currentPost->body]))->make();
        }
    }

    public function edit($postId)
    {
        if (!(new Auth($_SESSION))->check()) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        } else {

            $posts = (new Model('posts'))->selectWhere('id', $postId);
            if (count($posts) == 0) {
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
            } else {
                $currentPost = $posts[0];
                return (new View('backendpostupdate', (array) $currentPost))->make();
            }
        }
    }

    public function update($postId)
    {
        $posts = new Model('posts');
        if (!(new Auth($_SESSION))->check() || !$posts->selectWhere('id', $postId)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        } else {
            foreach ($_REQUEST as $key => $value) {
                $posts->updateWhere([$key => $value], ['id' => $postId]);
            }
            header("Location: {$_SERVER["HTTP_ORIGIN"]}/backend/posts/");

        }

    }
    public function destroy($id)
    {
        if (!(new Auth($_SESSION))->check()) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        } else {
            $posts = new Model('posts');
            $posts->deleteById($id);
        }
    }

}
