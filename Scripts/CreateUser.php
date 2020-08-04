<?php

//call

include_once __DIR__ ."/../Bootstrap.php";
include_once __DIR__ ."/../Model/Model.php";

$User = new Model('users');

if(count($User->selectWhere('username',$argv[1]))>0){
    print_r("Username already taken. User not created");
}
else {
    $User->new(['username'=>$argv[1],'password'=>password_hash($argv[1],PASSWORD_DEFAULT)]); 
    print_r("user successfully created");
    $user = $User->selectWhere('username',$argv[1])[0];
    unset($user->password);
    print_r( json_encode($user));
}

