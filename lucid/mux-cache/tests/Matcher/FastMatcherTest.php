<?php

namespace Lucid\Mux\Cache\Tests\Matcher;

use Lucid\Mux\Cache\Routes;
use Lucid\Mux\Cache\Matcher\Dumper;
use Lucid\Mux\RouteCollectionBuilder;
use Lucid\Mux\Cache\Matcher\FastMatcher;
use Lucid\Mux\Request\Context as Request;

class FastMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldMatchDumpedRouteMap()
    {
        $builder = new RouteCollectionBuilder;
        $builder->any('/', 'indexAction', ['route' => 'index']);
        $builder->get('/user', 'userIndexAction', ['route' => 'user.index']);
        $builder->get('/user/{id}', 'userShowAction', ['route' => 'user.show']);
        $builder->delete('/user/{area}/{id}', 'userDeleteAction', ['route' => 'user.delete']);
        $builder->get('/front', 'frontAction', ['route' => 'frontindex']);
        $builder->get('/login', 'loginIndexAction', ['route' => 'login.index']);
        $builder->post('/login', 'loginAction', ['route' => 'login.create']);


        $matcher = new FastMatcher($loader = $this->mockLoader());
        $routes = new Routes($builder->getCollection());

        $loader->method('load')->with($routes)->willReturnCallback(function ($routes) {
            $d = new Dumper;
            return eval('?>' . $d->dump($routes));
        });


        $request = new Request('/foo/bar', 'GET');
        $match = $matcher->matchRequest($request, $routes);
        $this->assertFalse($match->isMatch());

        $request = new Request('/', 'GET');
        $match = $matcher->matchRequest($request, $routes);
        $this->assertTrue($match->isMatch(), '/ GET');
        $this->assertEquals('indexAction', $match->getHandler());

        $request = new Request('/front', 'GET');
        $match = $matcher->matchRequest($request, $routes);
        $this->assertTrue($match->isMatch(), '/front GET');
        $this->assertEquals('frontAction', $match->getHandler());

        $request = new Request('/user/backstage/12', 'DELETE');
        $match = $matcher->matchRequest($request, $routes);
        $this->assertTrue($match->isMatch(), '/user/backstage/12 DELETE');
        $this->assertEquals('userDeleteAction', $match->getHandler());

        $request = new Request('/user/backstage/12', 'GET');
        $match = $matcher->matchRequest($request, $routes);
        $this->assertFalse($match->isMatch(), '/user/backstage/12 GET');
    }

    protected function setUp()
    {
        if (!defined('ARRAY_FILTER_USE_BOTH')) {
            $this->markTestSkipped('Insufficient HHVM version.');
        }
    }

    private function mockRequest()
    {
        return $this->getMockbuilder('Lucid\Mux\Request\ContextInterface')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockLoader()
    {
        return $this->getMockbuilder('Lucid\Mux\Cache\Matcher\MapLoader')
            ->disableOriginalConstructor()->getMock();
    }
}
