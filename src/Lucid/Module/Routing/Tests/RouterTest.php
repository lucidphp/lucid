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
use Lucid\Module\Routing\Router;
use Lucid\Module\Routing\Matcher\RequestMatcherInterface;

/**
 * @class RouterTest
 *
 * @package Lucid\Module\Routing\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Routing\RouterInterface', new Router($this->mockRoutes()));
    }

    /** @test */
    public function itShouldDispatchRouteAndReturnResponse()
    {
        $router = new Router(
            $routes = $this->mockRoutes(),
            $matcher = $this->mockMatcher(),
            $dispatcher = $this->mockHandlerDispatcher()
        );

        $request = $this->mockRequest();
        $context = $this->mockMatchContext(true);

        $matcher->shouldReceive('matchRequest')->with($request, $routes)->andReturn($context);
        $dispatcher->shouldReceive('dispatchHandler')->with($context)->andReturn('response');

        $resp = $router->dispatch($request);

        $this->assertSame('response', $resp);
    }

    /** @test */
    public function routeNamesShouldOnlyBeResolvedDuringDispatchPhase()
    {
        $router = new Router($this->mockRoutes(), $this->mockMatcher());

        $this->assertNull($router->getCurrentRoute());
        $this->assertNull($router->getCurrentRouteName());
    }

    /** @test */
    public function itShouldGetTheCurrentRouteName()
    {
        $router = new Router(
            $routes = $this->mockRoutes(),
            $matcher = $this->mockMatcher()
        );

        $routes->shouldReceive('has')->with('index')->andReturn(true);

        $first = null;
        $current = null;
        $handler = function () use ($router, &$current, &$first) {
            $first = $router->getFirstRouteName();
            $current = $router->getCurrentRouteName();
            $this->assertInstanceof('Lucid\Module\Routing\RouteInterface', $router->getFirstRoute());
            $this->assertInstanceof('Lucid\Module\Routing\RouteInterface', $router->getCurrentRoute());
            return 'response';
        };

        $request = $this->mockRequest();
        $context = $this->mockMatchContext(true, 'index', '/', $handler);

        $matcher->shouldReceive('matchRequest')->with($request, $routes)->andReturn($context);

        $resp = $router->dispatch($request);

        $this->assertSame('index', $first);
        $this->assertSame('index', $current);
    }

    /**
     * mockRequest
     *
     * @param string $path
     * @param string $method
     * @param string $query
     * @param string $host
     * @param string $scheme
     *
     * @return void
     */
    protected function mockRequest($path = '/', $method = 'GET', $query = '', $host = 'localhost', $scheme = 'http')
    {
        $r = m::mock('Lucid\Module\Routing\Http\RequestContextInterface');
        $r->shouldReceive('getPath')->andReturn($path);
        $r->shouldReceive('getMethod')->andReturn($method);
        $r->shouldReceive('getQueryString')->andReturn($query);
        $r->shouldReceive('getHost')->andReturn($host);
        $r->shouldReceive('getScheme')->andReturn($scheme);

        return $r;
    }

    protected function mockMatchContext($isMatch = false, $name = 'index', $url = '/', $handler = 'action', array $parameters = [])
    {
        $c = m::mock('Lucid\Module\Routing\Matcher\MatchContextInterface');

        $c->shouldReceive('isMatch')->andReturn($isMatch);
        $c->shouldReceive('getName')->andReturn($name);
        $c->shouldReceive('getPath')->andReturn($url);
        $c->shouldReceive('getHandler')->andReturn($handler);
        $c->shouldReceive('getParameters')->andReturn($parameters);

        return $c;
    }

    protected function mockMatcher()
    {
        $m = m::mock('Lucid\Module\Routing\Matcher\RequestMatcherInterface');
        $m->shouldIgnoreMissing();

        return $m;
    }

    protected function mockGenerator()
    {
        $m = m::mock('Lucid\Module\Routing\Http\UrlGeneratorInterface');
        $m->shouldIgnoreMissing();

        return $m;
    }

    protected function mockHandlerDispatcher()
    {
        $m = m::mock('Lucid\Module\Routing\Handler\HandlerDispatcherInterface');
        $m->shouldIgnoreMissing();

        return $m;
    }

    protected function mockRoutes()
    {
        $m = m::mock('Lucid\Module\Routing\RouteCollectionInterface');
        $m->shouldIgnoreMissing();

        $m->shouldReceive('get')->andReturnUsing(function ($name) use (&$m) {
            if ($m->has($name)) {
                return $this->mockRoute();
            }

            return null;
        });

        return $m;
    }

    protected function mockRoute()
    {
        $m = m::mock('Lucid\Module\Routing\RouteInterface');

        return $m;
    }

    protected function tearDown()
    {
        m::close();
    }
}
