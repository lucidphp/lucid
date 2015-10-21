<?php

/*
 * This File is part of the Lucid\Adapter\Twig\Tests\Extensions package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Twig\Tests\Extension;

use Mockery as m;
use Lucid\Adapter\Twig\Extension\RoutingExtension;

/**
 * @class RoutingExtensionTests
 *
 * @package Lucid\Adapter\Twig\Tests\Extensions
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RoutingExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Twig_Extension',
            new RoutingExtension($this->mockDispatcher(), $this->mockGenerator())
        );
    }

    /** @test */
    public function itShouldGetItsName()
    {
        $ext = $this->newExtension();
        $this->assertSame('lucid_routing', $ext->getName());
    }

    /** @test */
    public function itShouldGetItsFunctions()
    {
        $ext = $this->newExtension();
        $this->assertInternalType('array', $ext->getFunctions());
    }

    /** @test */
    public function itShouldRenderOutput()
    {
        $ext = $this->newExtension();
        $this->dispatcher->method('dispatchRoute')->with('route')->willReturn('ok');

        $this->assertSame('ok', $ext->displayRoute('route'));
    }

    /** @test */
    public function itShouldRenderOutputOfOutPutIsStreamed()
    {
        $ext = new RoutingExtension($disp = $this->mockCallableDispatcher(), $this->mockGenerator());

        $disp->shouldReceive('dispatchRoute')->with('route', m::any(), m::any())->andReturnUsing(function () {
            echo 'ok';
        });

        $this->assertSame('ok', $ext->displayRoute('route'));
    }

    /** @test */
    public function itShouldRenderEmptyStringIfErrord()
    {
        $ext = new RoutingExtension($disp = $this->mockCallableDispatcher(), $this->mockGenerator());

        $disp->shouldReceive('dispatchRoute')->with('route', m::any(), m::any())->andReturnUsing(function () {
            throw new \Exception;
        });

        $this->assertSame('', $ext->displayRoute('route'));
    }

    protected function newExtension()
    {
        return new RoutingExtension($this->dispatcher = $this->mockDispatcher(), $this->url = $this->mockGenerator());
    }

    protected function mockDispatcher()
    {
        return $this->getMock('Lucid\Routing\RouteDispatcherInterface');
    }

    protected function mockCallableDispatcher()
    {
        return m::mock('Lucid\Routing\RouteDispatcherInterface');
    }

    protected function mockGenerator()
    {
        return $this->getMock('Lucid\Routing\Http\UrlGeneratorInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
