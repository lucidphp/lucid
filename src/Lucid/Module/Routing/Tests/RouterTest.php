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
        $this->assertInstanceof('Lucid\Module\Routing\RouterInterface', new Router($this->mockMatcher()));
    }

    /** @test */
    public function itShouldDispatchRouteAndReturnResponse()
    {
        $router = new Router($matcher = $this->mockMatcher());

        $matcher->shouldReceive('matchRequest')
            ->andReturn([RequestMatcherInterface::MATCH, $context = $this->mockMatchContext()]);

        $resp = $router->dispatch($this->mockRequest());
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

    protected function mockMatchContext($name = 'index', $url = '/', $handler = 'action', array $parameters = [])
    {
        $c = m::mock('Lucid\Module\Routing\Matcher\MatchContextInterface');

        $c->shouldReceive('getName')->andReturn($name);
        $c->shouldReceive('getPath')->andReturn($url);
        $c->shouldReceive('getHandler')->andReturn($handler);
        $c->shouldReceive('getParameters')->andReturn($parameters);

        return $c;
    }

    protected function mockMatcher()
    {
        $m = m::mock('Lucid\Module\Routing\Matcher\RequestMatcherInterface');

        return $m;
    }

    protected function tearDown()
    {
        m::close();
    }
}
