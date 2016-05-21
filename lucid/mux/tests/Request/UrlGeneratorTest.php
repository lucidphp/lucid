<?php

namespace Lucid\Mux\Tests\Request;

use Lucid\Mux\Route;
use Lucid\Mux\Routes;
use Lucid\Mux\Request\UrlGenerator;
use Lucid\Mux\Request\Context as RequestContext;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
        /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Mux\Request\UrlGeneratorInterface', new UrlGenerator($this->mockRoutes()));
    }

    /**
     * @test
     * @dataProvider generatorProvider
     */
    public function itShouldGenerateUrls($name, $params, $host, $pattern, $rHost, $rDefaults, $expected)
    {
        $url = new UrlGenerator();
        $url->setRoutes($routes = $this->mockRoutes());

        $route = new Route($pattern, 'action', ['GET'], $rHost, $rDefaults);

        $has = [
            [$name, true]
        ];

        $get = [
            [$name, $route]
        ];

        $routes->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($has));
        $routes->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($get));

        $this->assertSame($expected, $url->generate($name, $params, $host));
    }

    /** @test */
    public function itShouldGetCurrentUrl()
    {
        $url = new UrlGenerator();
        $url->setRoutes($routes = $this->mockRoutes());

        $this->assertSame('/', $url->currentUrl());
        $this->assertSame('http://localhost/', $url->currentUrl(UrlGenerator::ABSOLUTE_PATH));

        $url->setRequestContext(new RequestContext('foo/bar', 'GET', '?foo=bar', 'example.com/'));

        $this->assertSame('foo/bar?foo=bar', $url->currentUrl());
        $this->assertSame('http://example.com/foo/bar?foo=bar', $url->currentUrl(UrlGenerator::ABSOLUTE_PATH));

        $this->assertNull($url->currentUrl('unknown_flag'));
    }

    /** @test */
    public function itShouldGenerateFullPaths()
    {
        $url = new UrlGenerator(
            new Routes(['foo' => $r = new Route('/foo', 'action', ['GET'], 'example.com')])
        );

        $this->assertSame('http://example.com/foo', $url->generate('foo', [], null, UrlGenerator::ABSOLUTE_PATH));

        $url = new UrlGenerator(
            new Routes(['foo' => $r = new Route('/foo', 'action', ['GET'])])
        );

        $this->assertSame(
            'http://example.com/foo',
            $url->generate('foo', [], 'example.com', UrlGenerator::ABSOLUTE_PATH)
        );

        $url = new UrlGenerator(
            new Routes(['foo' => $r = new Route('/foo', 'action', ['GET'])])
        );

        $this->assertSame('http://localhost/foo', $url->generate('foo', [], null, UrlGenerator::ABSOLUTE_PATH));
    }

    /** @test */
    public function generateShouldFail()
    {
        $url = new UrlGenerator();
        $url->setRoutes($routes = $this->mockRoutes());
        $routes->method('get')->willReturn(null);

        try {
            $url->generate('index');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('A route with name "index" could not be found.', $e->getMessage());
        }
    }

    public function generatorProvider()
    {
        return [
            ['index', [], null, '/', null, [], '/'],
            ['route', [], null, '/{param?}', null, [], '/'],
            ['route', ['id' => 12], null, '/{id}', null, [], '/12'],
            ['route', ['param' => 'lenny'], null, '/path/to/{param}', null, [], '/path/to/lenny'],
        ];
    }

    protected function mockRoutes()
    {
        return $this->createMock('Lucid\Mux\RouteCollectionInterface');
    }
}
