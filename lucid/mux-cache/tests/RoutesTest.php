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

use Lucid\Mux\Route;
use Lucid\Mux\Cache\Routes;
use Lucid\Mux\RouteInterface;
use Lucid\Mux\Routes as Collection;
use Lucid\Mux\RouteCollectionInterface;

class RoutesTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(RouteCollectionInterface::class, new Routes(new Collection([])));
    }

    /** @test */
    public function itShouldFindRoutesByStaticPath()
    {
        $ra = $this->mockRoute('/foo/bar');
        $rb = $this->mockRoute('/');

        $routes = new Routes(new Collection(['foo' => $ra, 'bar' => $rb]));

        $a = $routes->findByStaticPath('/');
        $b = $routes->findByStaticPath('/foo/bar');

        $this->assertSame([$ra], array_values($b->all()));
        $this->assertSame([$rb], array_values($a->all()));
    }

    /**
     * @param array $methods
     * @param array $schemes
     *
     * @return RouteInterface
     */
    protected function mockRoute(string $path, array $methods = ['GET'], array $schemes = ['http', 'https'])
    {
        $route = new Route($path, 'handler', $methods, null, $schemes);

        return $route;
    }
}
