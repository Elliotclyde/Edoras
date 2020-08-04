<?php

include_once __DIR__ . "/../Model/Model.php";

class Auth
{
    public function __construct($sessionData)
    {
        $this->sessionData = $sessionData;
        $this->bootstrapSelf();
    }

    public function check()
    {
        return $this->isLoggedIn;
    }

    public function user()
    {
        try {
            $matches = (new Model('users'))->selectWhere('id', $this->id);

            if (count($matches) < 1) {
                return new stdClass();
            }
        } catch (Exception $e) {return new stdClass();}

        return $matches[0];
    }
    public function id()
    {
        return $this->id;
    }
    public function login ($username,$password)
    {
        $matches = (new Model('users'))->selectWhere('username', $username);

        if (count($matches) < 1) {
            return false;
        }
        $user = $matches[0];
       
        if (!password_verify($password, $user->password)){
            return false;
        }
        foreach(array_keys((array)$user) as $key){
            $_SESSION[$key]= $user->{$key};
        }
        $_SESSION['isLoggedIn']=true;
        $_SESSION['id']=$user->id;
        $_SESSION['username']=$user->username;
        
        $this->sessionData = $_SESSION;
        $this->bootstrapSelf();
    }

    public function logout()
    {
        $this->sessionData = [];
        $this->bootstrapSelf();
        session_destroy();
    }

    private function bootstrapSelf()
    {
        if (count($this->sessionData) < 1) {
            $this->isLoggedIn=false;
            $this->id=-1;
            $this->username='guest';
        } 
        else {
            foreach ($this->sessionData as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

}
