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

use Lucid\Mux\Route;

/**
 * @class RouteTest
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\RouteInterface', $this->newRoute());
    }

    /** @test */
    public function itShouldGetPattern()
    {
        $route =  new Route($pattern = '/user/{id}', 'action');
        $this->assertSame($pattern, $route->getPattern());
    }

    /** @test */
    public function itShouldBeSerializable()
    {
        $route =  new Route(
            $pattern     = '/user/{id}',
            $handler     = 'action',
            $methods     = ['DELETE'],
            $host        = 'example.com',
            $defaults    = ['id' => 12],
            $constraints = ['id' => '\d+'],
            $schemes     = ['https']
        );

        $newRoute = unserialize(serialize($route));

        $this->assertSame($pattern, $newRoute->getPattern());
        $this->assertSame($handler, $newRoute->getHandler());
        $this->assertSame($methods, $newRoute->getMethods());
        $this->assertSame($host, $newRoute->getHost());
        $this->assertSame($defaults, $newRoute->getDefaults());
        $this->assertSame($constraints, $newRoute->getConstraints());
        $this->assertSame($schemes, $newRoute->getSchemes());
    }

    /** @test */
    public function itShouldThrowIfSerializedAndAnderIsClosure()
    {
        $route =  new Route('/', function () {
        });

        try {
            serialize($route);
        } catch (\LogicException $e) {
            $this->assertEquals('Cannot serialize handler.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldGetMethods()
    {
        $route =  new Route('/', 'action', ['POST']);

        $this->assertTrue($route->hasMethod('post'));

        $route =  new Route('/', 'action', $methods = ['get', 'head']);

        $this->assertTrue($route->hasMethod('get'));
        $this->assertTrue($route->hasMethod('head'));

        $this->assertSame(['GET', 'HEAD'], $route->getMethods());
    }

    /** @test */
    public function itShouldGetSchemes()
    {
        $route =  new Route('/', 'action');

        $this->assertTrue($route->hasScheme('http'));
        $this->assertTrue($route->hasScheme('https'));

        $route =  new Route('/', 'action', null, null, null, null, ['https']);
        $this->assertFalse($route->hasScheme('http'));
        $this->assertTrue($route->hasScheme('HTTPS'));

        $route =  new Route('/', 'action', null, null, null, null, null, ['HTTP', 'https']);

        $this->assertSame(['http', 'https'], $route->getSchemes());
    }

    /** @test */
    public function itShouldGetHost()
    {
        $route =  new Route('/', 'action');
        $this->assertNull($route->getHost());

        $route =  new Route('/', 'action', null, $host = 'example.com');
        $this->assertSame($host, $route->getHost());
    }

    /** @test */
    public function itShouldGetDefaults()
    {
        $route =  new Route('/', 'action', null, null, $def = ['foo' => 'bar']);

        $this->assertSame($def, $route->getDefaults());

        $this->assertSame('bar', $route->getDefault('foo'));
    }

    /** @test */
    public function itShouldGetConstraints()
    {
        $route =  new Route('/', 'action', null, null, null, $const = ['foo' => 'bar']);

        $this->assertSame($const, $route->getConstraints());

        $this->assertSame('bar', $route->getConstraint('foo'));
    }

    /** @test */
    public function itShouldCallParser()
    {
        $route = $this->getMockBuilder(Route::class)
            ->setConstructorArgs(['/', 'action'])
            ->setMethods(['getParserFunc'])
            ->getMock();
        $route->method('getParserFunc')->willReturnCallback(function () {
            return function () {
                return 'RouteContext';
            };
        });

        $this->assertSame('RouteContext', $route->getContext());
    }

    private function newRoute($path = '/', $handler = 'action')
    {
        return new Route($path, $handler);
    }
}
