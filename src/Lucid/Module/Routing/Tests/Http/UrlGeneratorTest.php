<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests\Http;

use Mockery as m;
use Lucid\Module\Routing\Route;
use Lucid\Module\Routing\Http\UrlGenerator;

/**
 * @class UrlGeneratorTest
 *
 * @package Lucid\Module\Routing\Tests\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Lucid\Module\Routing\Http\UrlGeneratorInterface',
            new UrlGenerator($this->mockRoutes())
        );
    }

    /**
     * @test
     * @dataProvider generatorProvider
     */
    public function itShouldGenerateUrls($name, $params, $host, $pattern, $rHost, $rDefaults, $expected)
    {
        $url = new UrlGenerator($routes = $this->mockRoutes());

        $route = new Route($pattern, 'action', 'GET', $rHost, $rDefaults);

        $routes->shouldReceive('has')->with($name)->andReturn(true);
        $routes->shouldReceive('get')->with($name)->andReturn($route);

        $this->assertSame($expected, $url->generate($name, $params, $host));
    }

    public function generatorProvider()
    {
        return [
            ['index', [], null, '/', null, [], '/'],
            ['route', [], null, '/{param?}', null, [], '/'],
            ['route', ['param' => 12], null, '/{param}', null, [], '/12'],
            ['route', ['param' => 'lenny'], null, '/path/to/{param}', null, [], '/path/to/lenny'],
        ];
    }

    protected function mockRoutes()
    {
        $routes = m::mock('Lucid\Module\Routing\RouteCollectionInterface');

        return $routes;
    }

    protected function tearDown()
    {
        m::close();
    }
}
