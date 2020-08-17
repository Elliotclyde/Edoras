# Edoras

A barebones PHP MVC framework. Inspired by laravel.

## Setup

### Database

First you will need to hook the app up to a database. How do you do this?

Create a .env file in the root of the project folder, and fill it with the following:

HOSTNAME=123.0.0.1   
DBNAME=mydatabasename   
DBUSERNAME=mydatabaseusername   
DBPASSWORD=mydatabasepassword   
DBPORT=mydatabaseport   

You will need to do your own migrations and seeds to create any database tables required. 

If you are going to use any authentication, you'll need a table called "users" with the columns id, username and password.

### Hosting

Host it up by pointing your apache/nginex to the "Public" folder. 

And that's the setup! Now you can start creating: 

## Using Edoras

There are only three folders you need worry about to build an application

- The "Public" folder where the index.php file lives and where you can keep static assets 
- The "Views" folder where you can build your templates 
- The "Controllers" folder where you can build controller classes to hold more complicated routing logic

## Routes

Define your routes in the "Public/Index.php" file by calling methods on the $router instance of the Router class.

### Simple Routes 

Calling a method on the $router instance will tell your application to respond:

- To a request with an HTTP method matching the (uppercased) name of the method called
- To a rerquest which also has a URL matching the first argument of the method call (a string)
- By outputting a response equalling the return value of the second argument (a closure)

### Simple Route Example 

`$router->get("/dog",function(){   
    return "woof";   
};`   

Will respond to a "GET" requst with the URL "yourapp/dog" with a body of "woof".

### Parameter Routes

And you can also grab one parameter from the route. You do this by throwing some curly braces around a sectionof the URL:

    $router->get("/dog/{$dogtype}",function($dogtype){   
        return "your type of dog is: " . $dogtype;   
    };  

The name in the route does not need to match the parameter of the closure: 

`$router->get("/dog/{$dogtype}",function($doggy){   
    return "your type of dog is: " . $doggy;
};`   

### View Routes 

You will often want to respond to a get request with a template from the Views folder.

You can do this by calling the "view" method on $router. This will respond:

- To a GET request which
- Has a URL matching the first argument of the method call (a string)
- With a response body equalling the output of a PHP file, and the contents of any other file  

### View Route Example   

$router->view("/dog","dog"); 

As you can see, you don't need to include the extension in the view filename. 

### Passing Variables to Views

You can pass variables to a PHP View template by passing an associative array of key-value pairs as the third argument.   

$router->view("/dog","dog",["chihuahua"=>"small" , "greatdane"=>"big"]);  

## Views

Create either static HTML or PHP files in the "Views" folder.

There is no fancy blade syntax, but try to use PHP the way it was originally intended: as a templating language. This will save headaches. 

### Instantiating Views

Instead of calling the view method on the $router, you can create instances of the View class for some more configurability.  

The constructor of the View class has the following arguments: 

- The filename in the Views folder (without the extension)
- An associative array of variables to pass to it if you're creating a PHP view.

### View Instantiation Example

    $router->get("/dog/{$dogtype}",function($dogtype){   
        $dogView = new View('dog', ['dogtype' => $dogtype])   
        return $dogView->make();   
    };  

The make() method creates a string which you can compose together as you like:

    $router->get("/dog/{$dogtype}",function($dogtype){   
        $body = (new View('dog', ['dogtype' => $dogtype]))->make();   
        $layout = new View('layout,['body' => $body]);   
        return $dogView->make();   
    };  

## Controllers

Create controllers by creating PHP files in the "Controllers" folder. Controllers: 

- Don't need to extend any controller parent class
- Need to have a classname matching their filename (excluding the extension)
- Should (but don't need to) have a name in PascalCase

Controller classes , however, the name of the controller class needs to match the filename (before the extension). It is also a good idea to use pascalcase as a convention for the controller name.  

Define public methods on the controller class. These will later be used to replace closures in routing.

### Controller Definition Example

    //Controllers/DogController.php  
    class DogController{
        public method index($dogtype){
            new View('dog', ['dogtype' => $dogtype]));
            return "Woof";
        }
    }    

### Controller Routes

Route to a controller method by calling a method on the $router instance in the "Public/Index.php". The name of the method and the first argument are the same as a [simple route](#simple-routes). 

To map to a controller replace the third argument (previously a closure) with a string with an "@" in it.

This will:

- Make a controller from the file in the "Controllers" directory matching the string BEFORE the "@" symbol
- Output the return value of the method matching the string AFTER the "@" symbol

### Controller Route Example

$router->get("/dog/{$dogtype}","DogController@index"};  

## Authentication

Edoras gives you an Auth class you can instatiate with the PHP $_SESSION global for authentication functionality.

### Logging In

When a user tries to log, call the "login" method:

- The first argument is the username
- The second argument is the password
- The return is a boolean on whether the login was a success
- On a success, the session will be saved as having logged in for future checks

### Log In Example

    $router->post('/backend', function () {
        if (new (Auth($_SESSION ))->login($_REQUEST["username"], $_REQUEST["password"]))) {  
            header("Location: {$_SERVER["HTTP_ORIGIN"]}/backend/");  
        } else {return <<<HTML  
        <h1>Sorry, you aren't logged in.</h1>  
      HTML;  
        }  
    });     

### Authentication checks

Want to check if the user is logged in? 

$auth->check() 

will return true if they are logged in.

### Creating Users

Edoras gives a simple script for the admin to create users. Create a user by running the following code in the command line:

php Scripts/CreateUser.php mycoolusername mycoolpassword

This will create a row for them in the user table.

## Models

Create a new model with the table name as a string for the constructor:

 $posts = new Model('posts');   

From which you can:

### Create New Database Rows
Create new item based on an associative array:  
posts->new(["title"=>"welovedogs","content"=>"We love dogs!!"]);   

### Select All Database Rows
Select all items:                                                   
posts->selectAll();   

### Select by Match
Select all where a key matches a value:                             
posts->selectWhere("title","welovedogs");

### Select by Id
Select all where a key matches a value:                             
posts->find(1);

### Update by Match 
Update a key to a value where another key matches another value. The first key-value pair is the change you want to make. The second is the check:
posts->updateWhere(["title"=>"dogsrus"],["id"=>1]);  

### Delete by ID
Delete an item by ID:                           
posts->deleteById(2);   

### Delete by Match
Delete all where a key matches a value:                             
posts->deleteWhere(postid,$id);   

If the key does not match a DB key an error will be thrown.   

All singlequotes are escaped.
