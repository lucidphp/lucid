<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Mux\Tests\Handler;

use Lucid\Mux\Matcher\Context;
use Lucid\Mux\Handler\Reflector;
use Lucid\Mux\Handler\Dispatcher;
use Lucid\Mux\Matcher\RequestMatcherInterface as M;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Mux\Handler\DispatcherInterface', new Dispatcher);
    }

    /** @test */
    public function itShouldDispatchHandler()
    {
        $called = false;
        $ctx = new Context(M::MATCH, 'route', $this->mockRequest('/foo'), 'foo@bar', [], []);

        $dispatcher = new Dispatcher($res = $this->mockResolver());
        $res->method('resolve')->with('foo@bar')->willReturn(new Reflector(function () use (&$called) {
            $called = true;
        }));

        $dispatcher->dispatch($ctx);
        $this->assertTrue($called);
    }

    private function mockResolver()
    {
        return $this->getMockbuilder('Lucid\Mux\Handler\ResolverInterface')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockReflector()
    {
        return $this->getMockbuilder('Ludic\Mux\Handler\Reflector')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockRequest($path = '/')
    {
        $r = $this->getMockbuilder('Lucid\Mux\Request\ContextInterface')
            ->disableOriginalConstructor()->getMock();

        $r->method('getPath')->willReturn($path);

        return $r;
    }
}
