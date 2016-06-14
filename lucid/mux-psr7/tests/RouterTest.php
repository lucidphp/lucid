<?php

/*
 * This File is part of the Lucid\Mux\Psr7 package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Psr7\Tests;

use Lucid\Mux\Route;
use Lucid\Mux\Routes;
use Lucid\Mux\Psr7\Router;
use Zend\Diactoros\ServerRequestFactory;
use Lucid\Mux\Tests\Request\Fixures\Server;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @class RouterTest
 *
 * @package Lucid\Mux\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            'Lucid\Mux\Psr7\RouterInterface',
            new Router($this->mockRoutes())
        );
    }

    /** @test */
    public function itShouldExpectAnPsrResponseToBeRreturned()
    {
        $router = new Router($routes = new Routes);

        $routes->add('foo', new Route('/', function () {
            return null;
        }));

        $request = $this->mockZendRequest(['REQUEST_URI' => '', 'HTTP_HOST' => 'example.com']);

        try {
            $router->handle($request);
        } catch (\Lucid\Mux\Exception\ResolverException $e) {
            /** @TODO fix err message in mux package */
            // $this->assertEquals('Cannot resolve handler of type "NULL".', $e->getMessage());
            $this->assertTrue(true);
            return;
        }

        $this->fail('test slipped');
    }

    /** @test */
    public function itShouldPassAnRequestObjectToTheMachedVars()
    {
        $router = new Router($routes = new Routes);

        $routes->add('foo', new Route('/foo/bar/{arg}', function ($request) {
            $this->assertInstanceOf(ServerRequestInterface::class, $request);
            return $this->getMockbuilder(ResponseInterface::class)
                ->getMock();

        }));

        $request = $this->mockZendRequest(['REQUEST_URI' => '/foo/bar/baz', 'HTTP_HOST' => 'example.com']);

        $router->handle($request);
    }

    private function mockZendRequest(array $server = [], array $get = [], array $request = [])
    {
        return ServerRequestFactory::fromGlobals(Server::mock($server));
    }

    private function mockRouter()
    {
        return $this->getMockbuilder('Lucid\Mux\RouterInterface')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockRoutes()
    {
        return $this->getMockbuilder('Lucid\Mux\RouteCollectionInterface')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockMapper()
    {
        return $this->getMockbuilder('Lucid\Mux\Psr7\ResponseMapperInterface')
            ->disableOriginalConstructor()->getMock();
    }
}
