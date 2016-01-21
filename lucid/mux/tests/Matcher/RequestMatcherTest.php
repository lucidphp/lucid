<?php

namespace Lucid\Mux\Tests\Matcher;

use Lucid\Mux\Route;
use Lucid\Mux\Routes;
use Lucid\Mux\Matcher\RequestMatcher as Matcher;

class RequestMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\Matcher\RequestMatcherInterface', new Matcher);
    }

    /** @test */
    public function itShouldMatchRequests()
    {
        $matcher = new Matcher;

        $routes = $this->mockRoutes();
        $routes->expects($this->once())->method('findByMethod')->willReturn($routes);
        $routes->expects($this->once())->method('findByScheme')->willReturn($routes);

        $routes->method('all')->willReturn(['foo' => $route = new Route('/foo/{user}/{id}', 'action')]);

        $request = $this->mockRequest();
        $request->method('getMethod')->willReturn('GET');
        $request->method('getPath')->willReturn('/foo/bar/12.2');
        $request->method('getScheme')->willReturn('http');

        $ret = $matcher->matchRequest($request, $routes);

        $this->assertTrue($ret->isMatch());
    }

    private function mockRoute()
    {
        return $this->getMockbuilder('Lucid\Mux\Route')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockRequest()
    {
        return $this->getMockbuilder('Lucid\Mux\Request\Context')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockRoutes()
    {
        return $this->getMockbuilder('Lucid\Mux\RouteCollectionInterface')
            ->disableOriginalConstructor()->getMock();
    }
}
