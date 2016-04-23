<?php

/*
 * This File is part of the Lucid\Signal package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Signal\Tests;

use Lucid\Signal\ContainerAwareDispatcher;
use Lucid\Signal\HandlerInterface;

/**
 * @class ContainerAwareDispatcherTest
 *
 * @package Lucid\Signal\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerAwareDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            'Lucid\Signal\EventDispatcherInterface',
            new ContainerAwareDispatcher($this->mockContainer())
        );
    }

    /** @test */
    public function itShouldAcceptServiceSignature()
    {
        $container = $this->mockContainer();
        $container->method('has')->with('myservice')->willReturn(true);
        $service = $this->getMockbuilder('MyService')
            ->setMethods(['handleEvent'])
            ->disableOriginalConstructor()->getMock();
        $service->expects($this->once())->method('handleEvent');
        $container->method('get')->with('myservice')->willReturn($service);

        $dispatcher = new ContainerAwareDispatcher($container);
        $dispatcher->addHandler('myevent', 'myservice@handleEvent');

        $this->assertTrue($dispatcher->hasEvent('myevent'));
        $dispatcher->dispatch('myevent');
    }

    /** @test */
    public function itShouldAcceptServiceSignatureWithoutAttachedMethod()
    {
        $container = $this->mockContainer();
        $container->method('has')->with('myservice')->willReturn(true);
        $container->method('get')->with('myservice')->willReturn(function () {
        });

        $dispatcher = new ContainerAwareDispatcher($container);
        $dispatcher->addHandler('myevent', 'myservice');

        $this->assertTrue($dispatcher->hasEvent('myevent'));
    }

    /** @test */
    public function itShouldNotAcceptServiceSignatureIfInvalid()
    {
        $container = $this->mockContainer();
        $container->method('has')->with('myservice')->willReturn(false);

        $dispatcher = new ContainerAwareDispatcher($container);

        try {
            $dispatcher->addHandler('myevent', 'myservice@handleMethod');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('Invalid handler "myservice@handleMethod".', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldResolveServiceHandlers()
    {
        $container = $this->mockContainer();
        $container->method('has')->with('myservice')->willReturn(true);
        $service = $this->getMockbuilder(HandlerInterface::class)
            ->disableOriginalConstructor()->getMock();
        $service->expects($this->once())->method('handleEvent');

        $container->method('get')->with('myservice')->willReturn($service);

        $dispatcher = new ContainerAwareDispatcher($container);
        $dispatcher->addHandler('myevent', 'myservice');
        $dispatcher->dispatch('myevent');
    }

    private function mockContainer()
    {
        return $this->getMockbuilder('Interop\Container\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
