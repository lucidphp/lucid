<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Tests;

use LogicException;
use Lucid\Mux\Cache\Routes;
use Lucid\Mux\Tests\RoutesTest as RouteCollectionTest;

class RoutesTest extends RouteCollectionTest
{
    /** @test */
    public function itShouldFindRoutesByStaticPath()
    {
        $ra = $this->mockRoute();
        $ra->getContext()->method('getStaticPath')->willReturn('/foo/bar');
        $rb = $this->mockRoute();
        $rb->getContext()->method('getStaticPath')->willReturn('/');

        $routes = $this->newRoutes(['foo' => $ra, 'bar' => $rb]);

        $a = $routes->findByStaticPath('/');
        $b = $routes->findByStaticPath('/foo/bar');

        $this->assertSame([$ra], array_values($b->all()));
        $this->assertSame([$rb], array_values($a->all()));
    }

    /** @test */
    public function itShouldThrowOnInvalidRouteName()
    {
        try {
            parent::routesShouldBeAddable();
        } catch (LogicException $e) {
            $this->assertEquals('Can\'t add routes to a cached collection.', $e->getMessage());
        }
    }

    /** @test */
    public function routesShouldBeAddable()
    {
        try {
            parent::routesShouldBeAddable();
        } catch (LogicException $e) {
            $this->assertEquals('Can\'t add routes to a cached collection.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldRemoveRoutes()
    {
        try {
            parent::itShouldRemoveRoutes();
        } catch (LogicException $e) {
            $this->assertEquals('Can\'t remove routes from a cached collection.', $e->getMessage());
        }
    }

    protected function newRoutes($routes = [])
    {
        return new Routes(parent::newRoutes($routes));
    }

    protected function mockRoute(array $methods = ['GET'], array $schemes = ['http', 'https'])
    {
        $route = parent::mockRoute($methods, $schemes);
        $route->method('getContext')->willReturn(
            $this->getMockBuilder('Lucid\Mux\RouteContext')
            ->disableOriginalConstructor()->getMock()
        );

        return $route;
    }
}
