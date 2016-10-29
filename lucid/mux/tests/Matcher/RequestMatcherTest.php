<?php declare(strict_types=1);

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

    /** @test */
    public function itShouldReturnCorrectMismatchReason()
    {
        $matcher = new Matcher();

        $routes = new Routes;

        $routes->add('user.show', new Route('/user/{id}', 'showUserAction', ['GET'], 'example.com'));
        $routes->add('user.delete', new Route('/user/{id}', 'deleteUserAction', ['DELETE'], 'example.{tld}'));

        $request = $this->mockRequest();

        $request->method('getMethod')->willReturn('PATCH');
        $request->method('getPath')->willReturn('/user/12');
        $request->method('getScheme')->willReturn('http');
        $request->method('getHost')->willReturn('example.com');

        $result = $matcher->matchRequest($request, $routes);

        $this->assertTrue($result->isMethodMismatch(), 'Reason should be method mismatch.');

        // replace delete
        $routes = new Routes;

        $routes->add(
            'user_delete',
            new Route('/user/{id}', 'deleteUserAction', ['DELETE'], 'example.org')
        );

        $request = $this->mockRequest();
        $request->method('getMethod')->willReturn('DELETE');
        $request->method('getPath')->willReturn('/user/12');
        $request->method('getScheme')->willReturn('http');
        $request->method('getHost')->willReturn('example.com');

        $result = $matcher->matchRequest($request, $routes);

        $this->assertTrue($result->isHostMismatch(), 'Reason should be host mismatch.');

        // replace delete
        $routes = new Routes;

        $routes->add(
            'index',
            new Route('/', 'indexAction', ['GET'], null, null, null, ['https'])
        );

        $request = $this->mockRequest();
        $request->method('getMethod')->willReturn('GET');
        $request->method('getPath')->willReturn('/');
        $request->method('getScheme')->willReturn('http');

        $result = $matcher->matchRequest($request, $routes);

        $this->assertTrue($result->isSchemeMismatch(), 'Reason should be scheme mismatch.');
    }

    /** @test */
    public function itShouldMapDefaultValues()
    {
        $matcher = new Matcher;
        $routes = new Routes;

        $routes->add(
            'user',
            new Route('/user/{id?}', 'getUserAction', ['GET'], null, ['id' => 12])
        );

        $request = $this->mockRequest();
        $request->method('getMethod')->willReturn('GET');
        $request->method('getPath')->willReturn('/user');
        $request->method('getScheme')->willReturn('http');

        $result = $matcher->matchRequest($request, $routes);

        $vars = $result->getVars();
        $this->assertArrayHasKey('id', $vars);
        $this->assertSame(12, $vars['id']);
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
