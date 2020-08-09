A barebones MVC framework for blogging. Mainly based on laravel.

How to hook it up to a database?

Create a .env file in the folder

HOSTNAME=123.0.0.1
DBNAME=mydatabasename
DBUSERNAME=mydatabaseusername
DBPASSWORD=mydatabasepassword
DBPORT=3mydatabaseport

Host by pointing your apache/nginex to the "Public" folder 

ROUTES

Define your routes in the "Public/Index.php" file by calling methods on the $router instance of the Router class. 

Use the http method as the method called, a string of the route as your first argument, then a closure which returns a string eg : 

$router->get("/dog",function(){
    return "woof;
};

Or use "view" to return an HTML file from the Views Folder on a get request: 

$router->view("/dog","dog");

This will work with a PHP file too (you don't need to note the filetype in the parameter). And you can pass variables with an associative array as the third parameter:

$router->view("/dog","dog",["chihuahua"=>"small" , "greatdane"=>"big"]);

And you can also grab one parameter from the route (only one for now sorry - I'm not Taylor Otwell!)

$router->get("/dog/{$dogtype}",function($dogtype){
    return "your type of dog is: " . $dogtype;
};

The name in the route does not need to match the parameter of the closure: 

$router->get("/dog/{$dogtype}",function($doggy){
    return "your type of dog is: " . $doggy;
};

VIEWS

Views can also be created with the View class:

$router->get("/dog/{$dogtype}",function(){

    $dogView = new View('dog', ['dogtype' => $dogtype])
    return $dogView->make();
};

The make() method creates a string which you can concatenate together as you like.

AUTHENTICATION

There's no register button. The login is designed for people who know the admin :) . These people can ask their admin to type the following into their command line: 

php Scripts/CreateUser.php mycoolusername mycoolpassword

And that will create a row for them in the user table

Want to check if the user is logged in? 

$auth->check() 

will return true.

MODELS

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

All singlequotes are escaped