<?php

namespace Lucid\Mux\Tests;

use Lucid\Mux\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\RouteInterface', $this->newRoute());
    }

    private function newRoute()
    {
        return new Route;
    }
}
