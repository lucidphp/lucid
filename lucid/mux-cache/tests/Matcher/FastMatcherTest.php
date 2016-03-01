<?php

namespace Lucid\Mux\Cache\Tests\Matcher;

use Lucid\Mux\Cache\Matcher\Dumper;
use Lucid\Mux\RouteCollectionBuilder;
use Lucid\Mux\Cache\Matcher\FastMatcher;
use Lucid\Mux\Request\Context as Request;

class FastMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldCompileMatchExpression()
    {
        $builder = new RouteCollectionBuilder;
        $builder->any('/', 'indexAction', ['route' => 'index']);
        $builder->get('/user', 'userIndexAction', ['route' => 'user.index']);
        $builder->get('/user/{id}', 'userShowAction', ['route' => 'user.show']);
        $builder->get('/front', 'frontAction', ['route' => 'front.index']);
        $builder->get('/login', 'loginIndexAction', ['route' => 'login.index']);
        $builder->post('/login', 'loginAction', ['route' => 'login.create']);
        $builder->delete('/user/{area}/{id}', 'userDeleteAction', ['route' => 'user.delete']);

        $routes = $builder->getCollection();

        $d = new Dumper;
        $map = $d->createMap($routes);
        $matcher = new FastMatcher($map);

        $request = new Request('/user/backoffice/12', 'DELETE');

        $match = $matcher->matchRequest($request, $routes);

        $this->assertTrue($match->isMatch());
    }
}
