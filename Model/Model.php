<?php

use function PHPUnit\Framework\throwException;

try {
  $connection = new PDO("mysql:host=".$_ENV["HOSTNAME"].";dbname=". $_ENV["DBNAME"], $_ENV["DBUSERNAME"], $_ENV["DBPASSWORD"]);
  // set the PDO error mode to exception
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  throw new Exception("Could not connect to database");
  echo "Connection failed: " . $e->getMessage();
}

$query = "SELECT * FROM posts WHERE slug = 'post-1'";

//get post by slug

$getPostBySlug = $connection->prepare('SELECT * FROM posts WHERE slug = :slug');
$getPostBySlug->execute(['slug' => "post-1"]);

//put in body by id

$putBodyById = $connection->prepare('UPDATE posts SET body=:body WHERE id = :id');
$putBodyById->execute(['id' => "1",'body'=>'And now it\'s back to normal']);

//get post by slug

$getPostById = $connection->prepare('SELECT * FROM posts WHERE id = :id');
$getPostById->execute(['id' => 1]);

foreach ($getPostById as $row){
  echo $row["id"];
  echo $row["slug"];
  echo $row["title"];
  echo $row["body"];
}

class Model
{



}