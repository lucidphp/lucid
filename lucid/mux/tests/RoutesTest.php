<?php

namespace Lucid\Mux\Tests;

use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\RouteCollectionMutableInterface;
use Lucid\Mux\RouteInterface;
use Lucid\Mux\Routes;

class RoutesTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $routes = $this->newRoutes();
        $this->assertInstanceOf(RouteCollectionInterface::class, $routes);
        $this->assertInstanceOf(RouteCollectionMutableInterface::class, $routes);
    }

    /** @test */
    public function routesShouldBeAddable()
    {
        $routes = $this->newRoutes();
        $routes->add('foo', $r = $this->mockRoute());

        $this->assertSame(['foo' => $r], $routes->all());
    }

    /** @test */
    public function itShouldFindARouteByName()
    {
        $ra = $this->mockRoute();
        $rb = $this->mockRoute();
        $routes = $this->newRoutes(['foo' => $ra, 'bar' => $rb]);

        $this->assertTrue($ra === $routes->get('foo'));
    }

    /** @test */
    public function itShouldFindARoutesByMethod()
    {
        $ra = $this->mockRoute(['GET']);
        $rb = $this->mockRoute(['POST']);
        $routes = $this->newRoutes(['foo' => $ra, 'bar' => $rb]);

        $this->assertSame(['bar' => $rb], $routes->findByMethod('POST')->all());
    }

    /** @test */
    public function itShouldReturnEmptyCollection()
    {
        $routes = new Routes(['foo' => $this->mockRoute(['GET'], ['HTTPS'])]);

        $this->assertEmpty($routes->findByMethod('post')->all());
        $this->assertEmpty($routes->findByScheme('http')->all());
    }


    /** @test */
    public function itShouldRemoveRoutes()
    {
        $ra = $this->mockRoute(['GET']);
        $rb = $this->mockRoute(['POST']);
        $routes = $this->newRoutes(['foo' => $ra, 'bar' => $rb]);

        $this->assertTrue($routes->has('bar'));
        $routes->remove('bar');
        $this->assertFalse($routes->has('bar'));

        $routes->remove('bar');
        $this->assertTrue($routes->has('foo'));

        $routes->remove('foo');
        $this->assertFalse($routes->has('foo'));
    }

    /** @test */
    public function itShouldFindARoutesByScheme()
    {

        $ra = $this->mockRoute(['GET'], ['http']);
        $rb = $this->mockRoute(['GET'], ['https', 'http']);
        $routes = $this->newRoutes(['foo' => $ra, 'bar' => $rb]);

        $this->assertSame(['bar' => $rb], $routes->findByScheme('https')->all());
    }

    protected function mockRoute(array $methods = ['GET'], array $schemes = ['http', 'https'])
    {
        $route = $this->getMockBuilder(RouteInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $route->method('getPattern')->willReturn('/');
        $route->method('getSchemes')->willReturn($schemes);
        $route->method('getMethods')->willReturn($methods);
        $route->method('getHost')->willReturn(null);
        $route->method('getHandler')->willReturn('handler');

        $route->method('hasMethod')->willReturnCallback(function ($m) use ($methods) {
            return in_array(strtoupper($m), $methods);
        });

        $route->method('hasScheme')->willReturnCallback(function ($s) use ($schemes) {
            return in_array(strtoupper($s), $schemes);
        });

        return $route;
    }

    protected function newRoutes($routes = [])
    {
        return new Routes($routes);
    }
}
