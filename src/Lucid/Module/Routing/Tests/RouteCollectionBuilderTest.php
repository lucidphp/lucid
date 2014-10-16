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

use Lucid\Module\Routing\RouteCollectionBuilder;

/**
 * @class RouteCollectionBuilderTest
 *
 * @package Lucid\Module\Routing\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteCollectionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Routing\RouteCollectionBuilder', new RouteCollectionBuilder);
    }

    /**
     * @test
     * @dataProvider methodProvider
     */
    public function itShouldRoutesWithMethodShortCuts($method, $args, $expected)
    {
        $builder = $this->newBuilder();

        call_user_func_array([$builder, $method], $args);

        $routes = $builder->getCollection();

        $this->assertTrue((bool)($route = $routes->get($args[2]['name'])));
        foreach ($expected as $m) {
            $this->assertTrue($route->hasMethod($m));
        }
    }

    /** @test */
    public function itShouldBuildGroups()
    {
        $builder = $this->newBuilder();

        $builder->group('/secure', ['schemes' => 'https'], function ($builder) {
            $builder->get('login', 'action_login', ['name' => 'login.index']);
            $builder->delete('login', 'action_logout', ['name' => 'logout.action']);
        });

        $routes = $builder->getCollection();

        $this->assertTrue((bool)($a = $routes->get('login.index')));
        $this->assertTrue((bool)($b = $routes->get('logout.action')));

        // test for prepended path prefix
        $this->assertSame('/secure/login', $a->getPattern());
        $this->assertSame('/secure/login', $b->getPattern());

        // test for shared schemes
        $this->assertSame(['https'], $a->getSchemes());
        $this->assertSame(['https'], $b->getSchemes());

    }

    public function methodProvider()
    {
        return [
            ['get', ['/', 'action_get', ['name' => 'get']], ['GET']],
            ['post', ['/', 'action_post', ['name' => 'post']], ['POST']],
            ['put', ['/', 'action_put', ['name' => 'put']], ['PUT']],
            ['head', ['/', 'action_head', ['name' => 'head']], ['HEAD']],
            ['patch', ['/', 'action_patch', ['name' => 'patch']], ['PATCH']],
            ['delete', ['/', 'action_delete', ['name' => 'delete']], ['DELETE']],
            ['any', ['/', 'action_any', ['name' => 'any']], ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE']],
        ];
    }

    protected function newBuilder()
    {
        return new RouteCollectionBuilder;
    }
}
