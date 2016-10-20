<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Tests;

use Lucid\Mux\Matcher\ContextInterface;
use Lucid\Mux\Router;
use Lucid\Mux\Routes;
use Lucid\Mux\RouteInterface;
use Lucid\Mux\RouterInterface;
use Lucid\Mux\Matcher\Context;
use Lucid\Mux\Request\UrlGenerator;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Exception\MatchException;
use Lucid\Mux\Request\Context as Request;
use Lucid\Mux\Handler\DispatcherInterface;
use Lucid\Mux\Matcher\RequestMatcherInterface;
use Lucid\Mux\Request\ResponseMapperInterface;
use Lucid\Mux\Request\UrlGeneratorInterface as Url;

/**
 * Class RouterTest
 * @package Lucid\Mux\Tests
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(RouterInterface::class, new Router($this->mockRoutes()));
    }

    /** @test */
    public function itShouldGetUrlGenerator()
    {
        $router = new Router($this->mockRoutes(), null, null, null, $url = $this->mockUrl());

        $url->expects($this->once())->method('generate')->with('foo', [], null, Url::RELATIVE_PATH)
            ->willReturn('bar');
        $router->getUrl('foo');
    }

    /** @test */
    public function itShouldDispatchRoute()
    {
        $route = $this->mockRoute();
        $route->method('getMethods')->willReturn(['GET']);
        $route->method('getSchemes')->willReturn(['http']);

        $router = new Router(
            $routes = new Routes(['foo' => $route]),
            null,
            $dispatcher = $this->mockDispatcher(),
            null,
            $url = $this->mockUrl()
        );

        $url->expects($this->once())->method('generate')->willReturnCallback(function ($name) {
            if ('foo' !== $name) {
                $this->fail();
            }

            return '/foo/bar';
        });

        $dispatcher->expects($this->once())
            ->method('dispatch')->willReturnCallback(function ($context) use ($router, $route) {
                if ('/foo/bar' !== $context->getPath()) {
                    $this->fail();
                }

                /*
                 * @todo iwyg <mail@thomas-appel.com>; Fr 22 Jan 20:42:04 2016 -->
                 * create a explicit testcase
                 */
                $this->assertSame('foo', $router->getFirstRouteName());
                $this->assertSame('foo', $router->getCurrentRouteName());

                return 'ok';
            });

        $this->assertSame('ok', $router->route('foo'));
    }

    /** @test */
    public function itShouldDispatchRequest()
    {
        $req = new Request('/');
        $router = new Router(
            $routes = $this->mockRoutes(),
            $matcher = $this->mockMatcher(),
            $dispatcher = $this->mockDispatcher()
        );

        $dispatcher->expects($this->once())->method('dispatch')->with()->willReturnCallback(function () {
            return 'ok';
        });

        $matcher->expects($this->once())->method('matchRequest')->with($req, $routes)
            ->willReturn($match = $this->mockMatch(true));

        $match->method('getRequest')->willReturn($req);

        $this->assertSame('ok', $router->dispatch($req));
    }

    /** @test */
    public function itMapResponse()
    {
        $req = new Request('/');
        $router = new Router(
            $routes = $this->mockRoutes(),
            $matcher = $this->mockMatcher(),
            $dispatcher = $this->mockDispatcher(),
            $mapper = $this->mockMapper()
        );

        $dispatcher->expects($this->once())->method('dispatch')->with()->willReturnCallback(function () {
            return 'ok';
        });

        $matcher->expects($this->once())->method('matchRequest')->with($req, $routes)
            ->willReturn($match = $this->mockMatch(true));

        $match->method('getRequest')->willReturn($req);

        $mapper->expects($this->once())->method('mapResponse')->with('ok')->willReturn('ko');

        $this->assertSame('ko', $router->dispatch($req));
    }

    /** @test */
    public function itShouldThrowOnNoneMatchingRequest()
    {
        $req = new Request('/foo');
        $router = new Router($routes = $this->mockRoutes(), $matcher = $this->mockMatcher());

        $matcher->expects($this->once())->method('matchRequest')->with($req, $routes)
            ->willReturn($this->mockMatch(false));

        try {
            $router->dispatch($req);
        } catch (MatchException $e) {
            $this->assertInstanceOf(ContextInterface::class, $e->getMatchContext());
            $this->assertEquals('No route found for requested resource "/foo".', $e->getMessage());
        }
    }

    private function mockMatch(bool $matches = true)
    {
        $m = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();

        $m->method('isMatch')->willReturn($matches);

        return $m;
    }

    private function mockMatcher()
    {
        return $this->getMockBuilder(RequestMatcherInterface::class)
            ->disableOriginalConstructor()->getMock();
    }

    private function mockRoutes()
    {
        return $this->getMockBuilder(RouteCollectionInterface::class)
            ->disableOriginalConstructor()->getMock();
    }

    private function mockRoute()
    {
        return $this->getMockBuilder(RouteInterface::class)
            ->disableOriginalConstructor()->getMock();
    }

    private function mockMapper()
    {
        return $this->getMockBuilder(ResponseMapperInterface::class)
            ->disableOriginalConstructor()->getMock();
    }

    private function mockDispatcher()
    {
        return $this->getMockBuilder(DispatcherInterface::class)
            ->disableOriginalConstructor()->getMock();
    }

    private function mockUrl()
    {
        return $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()->getMock();
    }
}
