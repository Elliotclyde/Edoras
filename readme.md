# Minivel

A barebones MVC framework for blogging. Mainly based on laravel.

## Setup

### Database

First you will need to hook the app up to a database. How do you do this?

Create a .env file in the root of the project folder, and fill it with the following:

HOSTNAME=123.0.0.1
DBNAME=mydatabasename
DBUSERNAME=mydatabaseusername
DBPASSWORD=mydatabasepassword
DBPORT=mydatabaseport

Then you will need to do your own migrations and seeds to create any database tables you need. 

If you are going to use any auth, you'll need a table called "users" with the columns id, username and password.

If you are going to hold some blog posts, you'll need a table called "posts" with the columns id, title, body, and slug. 

### hosting

Host it up by pointing your apache/nginex to the "Public" folder. 

And that's the setup! Now you can start creating: 

## Routes

Define your routes in the "Public/Index.php" file by calling methods on the $router instance of the Router class. 

Use the http method as the method called, a string of the route as your first argument, then a closure which returns a string eg : 

$router->get("/dog",function(){
    return "woof";
};

Or call the "view" method to return an HTML file from the Views Folder on a get request: 

$router->view("/dog","dog");

This will work with a PHP file too (you don't need to note the filetype in the parameter). And you can pass variables with an associative array as the third parameter on the view method:

$router->view("/dog","dog",["chihuahua"=>"small" , "greatdane"=>"big"]);

And you can also grab one parameter from the route (only one for now). You do this by throwing some curly braces around a sectionof the URL: 

$router->get("/dog/{$dogtype}",function($dogtype){
    return "your type of dog is: " . $dogtype;
};

The name in the route does not need to match the parameter of the closure: 

$router->get("/dog/{$dogtype}",function($doggy){
    return "your type of dog is: " . $doggy;
};

## Views

Views can also be created with the View class:

$router->get("/dog/{$dogtype}",function(){
    $dogView = new View('dog', ['dogtype' => $dogtype])
    return $dogView->make();
};

The Views constructor arguments are: 1. The filename in the Views folder (without the extension), and 2. An associative array of variables to pass to it if you're creating a PHP view.

The make() method creates a string which you can compose together as you like:

$router->get("/dog/{$dogtype}",function(){
    $body = (new View('dog', ['dogtype' => $dogtype]))->make();
    $layout = new View('layout,['body' => $body]);
    return $dogView->make();
};

When you create your view files, use static HTML or write them the way PHP was originally written to be used: As a templating language. Anything you echo will be returned as a string to wherever you are calling the "make" method.

## Authentication

There's no register button. The login is designed for people who know the admin :) . These people can ask their admin to type the following into their command line: 

php Scripts/CreateUser.php mycoolusername mycoolpassword

And that will create a row for them in the user table

Want to check if the user is logged in? 

$auth->check() 

will return true.

## Models

Create a new model with the table name as a string for the constructor:

 $posts = new Model('posts');

From which you can: 

create new item based on an associative array:                      posts->new(["title"=>"welovedogs","content"=>"We love dogs!!"]);
select all items:                                                   posts->selectAll();
select all where a key matches a value:                             posts->selectWhere(postid,$id);
update a key to a value where another key matches another value:    posts->updateWhere(["title"=>"dogsrus"],["id"=>1]);
select all where a key matches a value:                             posts->updateWhere(["title"=>"dogsrus"],["id"=>1]);
delete all where a key matches a value:                             posts->deleteWhere(postid,$id);

If the key does not match a DB key an error will be thrown. 

All singlequotes are escaped.