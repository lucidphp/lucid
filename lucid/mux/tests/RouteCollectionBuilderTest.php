<?php

namespace Lucid\Mux\Tests;

use Lucid\Mux\RouteCollectionBuilder as Builder;

class RouteCollectionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Mux\RouteCollectionBuilder', new Builder);
    }

    /** @test */
    public function itShouldAutoGenerateNames()
    {
        $builder = $this->newBuilder();
        $builder->get('/app/{user}/{area}', 'action', ['user' => '\w+', 'area' => '\d+']);

        $name = current(array_keys($builder->getCollection()->all()));

        $this->assertStringStartsWith('route_GET_', $name);

        $builder = $this->newBuilder();
        $builder->post('/app/{user}/{area}', 'action', $c = ['user' => '\w+', 'area' => '\d+']);

        $name = current(array_keys($builder->getCollection()->all()));

        $this->assertStringStartsWith('route_POST_', $name);
    }

    /** @test */
    public function itShouldObtainDefaults()
    {
        $builder = $this->newBuilder();
        $builder->get('/{foo}', 'action', [Builder::K_NAME => 'index'], ['foo' => 'bar']);

        $this->assertSame('bar', $builder->getCollection()->get('index')->getDefault('foo'));
    }

    /** @test */
    public function itShouldObtainConstraints()
    {
        $builder = $this->newBuilder();
        $builder->get('/{foo}', 'action', [Builder::K_NAME => 'index', 'foo' => '\w+']);

        $this->assertSame('\w+', $builder->getCollection()->get('index')->getConstraint('foo'));
    }

    /**
     * @test
     * @dataProvider methodProvider
     */
    public function itShouldCreateRoutesWithMethodShortCuts($method, $args, $expected)
    {
        $builder = $this->newBuilder();

        call_user_func_array([$builder, $method], $args);

        $routes = $builder->getCollection();

        $this->assertTrue((bool)($route = $routes->get($args[2][Builder::K_NAME])));

        foreach ($expected as $m) {
            $this->assertTrue($route->hasMethod($m));
        }
    }

    /** @test */
    public function itShouldObtainDefaultSchemes()
    {
        $builder = $this->newBuilder();
        $builder->get('/', 'action');

        $this->assertEquals(['http', 'https'], current($builder->getCollection()->all())->getSchemes());
    }

    /** @test */
    public function itShouldBuildGroups()
    {
        $builder = $this->newBuilder();

        $builder->group('/secure', [Builder::K_SCHEME => 'https'], function ($builder) {
            $builder->get('login', 'action_login', [Builder::K_NAME => 'login.index']);
            $builder->delete('login', 'action_logout', [Builder::K_NAME => 'logout.action']);
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


        $builder = $this->newBuilder();
        $builder->group('/backstage', [Builder::K_SCHEME => 'https']);
        $builder->get('users', 'action_users', [Builder::K_NAME => 'panel']);
        $builder->endGroup();

        $this->assertInstanceOf('Lucid\Mux\RouteInterface', $route = $builder->getCollection()->get('panel'));
        $this->assertEquals('/backstage/users', $route->getPattern());
    }

    /** @test */
    public function itShouldPassGroupRequirements()
    {
        $builder = $this->newBuilder();

        $builder->group('backstage', [Builder::K_HOST => 'example.com', Builder::K_SCHEME => 'https']);
        $builder->addRoute('GET', 'foo', 'fooAction', [Builder::K_NAME => 'foo']);
        $builder->endGroup();

        $routes = $builder->getCollection();

        $this->assertSame('example.com', $routes->get('foo')->getHost());
        $this->assertSame(['https'], $routes->get('foo')->getSchemes());
    }

    public function methodProvider()
    {
        return [
            ['get', ['/', 'action_get', [Builder::K_NAME => 'get']], ['GET']],
            ['post', ['/', 'action_post', [Builder::K_NAME => 'post']], ['POST']],
            ['put', ['/', 'action_put', [Builder::K_NAME => 'put']], ['PUT']],
            ['head', ['/', 'action_head', [Builder::K_NAME => 'head']], ['HEAD']],
            ['patch', ['/', 'action_patch', [Builder::K_NAME => 'patch']], ['PATCH']],
            ['delete', ['/', 'action_delete', [Builder::K_NAME => 'delete']], ['DELETE']],
            ['any', ['/', 'action_any', [Builder::K_NAME => 'any']], ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE']],
        ];
    }

    protected function newBuilder()
    {
        return new Builder;
    }

    protected function setUp()
    {
        if (defined('HHVM_VERSION') && version_compare(HHVM_VERSION, '3.8.1', '<')) {
            $this->markTestSkipped(sprintf('Unsupported HHVM version %s', HHVM_VERSION));
        }
    }
}
