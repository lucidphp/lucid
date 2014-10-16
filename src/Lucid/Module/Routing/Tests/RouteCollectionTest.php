<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests;

use Mockery as m;
use Lucid\Module\Routing\RouteCollection;

/**
 * @class RouteCollectionTest
 *
 * @package Lucid\Module\Routing\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $routes = new RouteCollection;
        $this->assertInstanceof('Lucid\Module\Routing\RouteCollectionInterface', $routes);
    }

    /** @test */
    public function routesShouldBeAddable()
    {
        $routes = new RouteCollection;
        $routes->add('foo', $r = $this->mockRoute());

        $this->assertSame(['foo' => $r], $routes->all());
    }

    /** @test */
    public function itShouldFindARouteByName()
    {
        $routes = new RouteCollection;

        $routes->add('foo', $ra = $this->mockRoute());
        $routes->add('bar', $rb = $this->mockRoute());

        $this->assertSame($ra, $routes->get('foo'));
        $this->assertSame($rb, $routes->findByName('bar'));
    }

    /** @test */
    public function itShouldFindARoutesByMethod()
    {
        $routes = new RouteCollection;

        $routes->add('foo', $ra = $this->mockRoute());
        $routes->add('bar', $rb = $this->mockRoute());

        $ra->shouldReceive('hasMethod')->with('GET')->andReturn(false);
        $rb->shouldReceive('hasMethod')->with('GET')->andReturn(true);

        $this->assertSame(['bar' => $rb], $routes->findByMethod('GET')->all());
    }

    /** @test */
    public function itShouldFindARoutesByScheme()
    {
        $routes = new RouteCollection;

        $routes->add('foo', $ra = $this->mockRoute());
        $routes->add('bar', $rb = $this->mockRoute());

        $ra->shouldReceive('hasScheme')->with('https')->andReturn(false);
        $rb->shouldReceive('hasScheme')->with('https')->andReturn(true);

        $this->assertSame(['bar' => $rb], $routes->findByScheme('https')->all());
    }

    protected function tearDown()
    {
        m::close();
    }

    protected function mockRoute()
    {
        return m::mock('Lucid\Module\Routing\RouteInterface');
    }
}
