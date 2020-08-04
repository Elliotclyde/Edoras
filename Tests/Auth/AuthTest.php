<?php

include_once 'Bootstrap.php';
include_once 'Auth/Auth.php';

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testAuthCanCheckIfUserIsLoggedIn(){
        $loggedInAuth = new Auth( $this->getLoggedInSession());
        $this->assertTrue($loggedInAuth->check());
        
        $loggedOutAuth = new Auth( $this->getLoggedOutSession());
        $this->assertFalse($loggedOutAuth->check());
    }
    public function testAuthCanGiveModelOfUser(){
        $loggedInAuth = new Auth( $this->getLoggedInSession());
        $this->assertObjectHasAttribute("username",$loggedInAuth->user());
        $this->assertObjectHasAttribute("id",$loggedInAuth->user());

        $loggedOutAuth = new Auth( $this->getLoggedOutSession());
        $this->assertObjectNotHasAttribute("username",$loggedOutAuth->user());
        $this->assertObjectNotHasAttribute("id",$loggedOutAuth->user());

    }

    public function testAuthCanGiveIdOfLoggedInUser(){
        $loggedInAuth = new Auth( $this->getLoggedInSession());
        $this->assertTrue($loggedInAuth->id()==1);
    }

    public function testAuthCanLogin(){
        $auth = new Auth( $this->getLoggedOutSession());
        $this->assertFalse($auth->check());

        $auth->login('test','test');
        $this->assertTrue($auth->check());
    }

    public function testAuthCanLogout(){
        $auth = new Auth( $this->getLoggedInSession());
        $this->assertTrue($auth->check());
        
        $auth->logout();
        $this->assertFalse($auth->check());
    }
    //username: test password: test id: 1

    private function getLoggedInSession(){
        return ['isLoggedIn'=>true,'username'=>'test','id'=>1];
    }
    private function getLoggedOutSession(){
        return [];
    }
}
