<?php

/*
 * This File is part of the Routing\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests;

use Lucid\Module\Routing\Route;
use Lucid\Module\Routing\RouteParser;

/**
 * @class RouteTest
 *
 * @package Routing\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $route = new Route('/', 'action');
        $this->assertInstanceof('Lucid\Module\Routing\RouteInterface', $route);
    }

    /** @test */
    public function itShouldGetItsMethodAsString()
    {
        $route = new Route('/', 'action');
        $this->assertSame(['GET'], $route->getMethods());
        $route = new Route('/', 'action', 'GET|POST');
        $this->assertSame(['GET', 'POST'], $route->getMethods());

        $route = new Route('/', 'action', ['get', 'post']);
        $this->assertContains('GET', $route->getMethods());
        $this->assertContains('POST', $route->getMethods());
    }

    /** @test */
    public function itShouldGetThePatternString()
    {
        $route = new Route('/', 'action');
        $this->assertSame('/', $route->getPattern());
    }

    /** @test */
    public function itShouldGetItsHandler()
    {
        $route = new Route('/', 'action');
        $this->assertSame('action', $route->getHandler());
    }

    /** @test */
    public function itShouldGetItsHost()
    {
        $route = new Route('/', 'action');
        $this->assertNull($route->getHost());

        $route = new Route('/', 'action', 'GET', 'domain.com');
        $this->assertSame('domain.com', $route->getHost());
    }

    /** @test */
    public function itShouldGetDefaults()
    {
        $route = new Route('/', 'action');
        $this->assertSame([], $route->getDefaults());

        $route = new Route('/', 'action', 'GET', null, ['foo' => 'bar']);
        $this->assertSame('bar', $route->getDefault('foo'));
    }

    /** @test */
    public function itShouldGetConstraits()
    {
        $route = new Route('/', 'action');
        $this->assertSame([], $route->getConstraints());

        $route = new Route('/', 'action', 'GET', null, [], ['foo' => 'bar']);
        $this->assertSame('bar', $route->getConstraint('foo'));
    }

    /** @test */
    public function itShouldGetSchemes()
    {
        $route = new Route('/', 'action');

        $this->assertSame(['http', 'https'], $route->getSchemes());

        $route = new Route('/', 'action', 'GET', null, [], [], 'https');

        $this->assertSame(['https'], $route->getSchemes());

        $route = new Route('/', 'action', 'GET', null, [], [], 'http|https');
        $this->assertContains('http', $route->getSchemes());
        $this->assertContains('https', $route->getSchemes());

        $route = new Route('/', 'action', 'GET', null, [], [], ['HTTP', 'HTTPS']);
        $this->assertContains('http', $route->getSchemes());
        $this->assertContains('https', $route->getSchemes());
    }

    /** @test */
    public function itShouldParseToContext()
    {
        $route = new Route('/', 'action');

        $this->assertInstanceof('Lucid\Module\Routing\RouteContextInterface', $route->getContext());
    }

    /** @test */
    public function itShouldBeParsedWhenSerialized()
    {
        $route = new Route('/', 'action');

        $s = serialize($route);

        preg_match('~RouteContext\"\:~', $s, $matches);

        $this->assertTrue(isset($matches[0]));
    }

    /** @test */
    public function itShouldThrowIfHandlerIsNotSerializable()
    {
        $route = new Route(
            '/',
            function () {
            }
        );

        try {
            serialize($route);
        } catch (\RuntimeException $e) {
            $this->assertSame('Cannot serialize handler.', $e->getMessage());
        }
    }
}
