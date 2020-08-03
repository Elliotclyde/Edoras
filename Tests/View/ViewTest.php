<?php

include_once 'View/View.php';

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testWillReturnHomeView()
    {
         $this->assertIsString(
            (new View('home'))->make()
        );
        
    }
}