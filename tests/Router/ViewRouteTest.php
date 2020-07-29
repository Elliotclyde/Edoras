<?php

include 'Router/ViewRoute.php';

use PHPUnit\Framework\TestCase;

class ViewRouteTest extends TestCase
{
    public function testCanBeCreatedFromValidEmailAddress()
    {
        $this->assertInstanceOf(
            ViewRoute::class,
            new ViewRoute(['/', "home",['dog'=>'fish']])
        );
    }
}
