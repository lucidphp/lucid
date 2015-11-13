<?php

namespace Lucid\Signal\Tests;

use Lucid\Signal\ContainerAwareDispatcher;

class ContainerAwareDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Signal\EventDispatcherInterface', new ContainerAwareDispatcher($this->mockContainer()));
    }

    /** @test */
    public function itShouldAcceptServiceSignature()
    {
        $container = $this->mockContainer();
        $container->method('has')->with('myservice')->willReturn(true);

        $dispatcher = new ContainerAwareDispatcher($container);
        $dispatcher->addHandler('myevent', 'myservice@handleEvent');

        $this->assertTrue($dispatcher->hasEvent('myevent'));
    }

    /** @test */
    public function itShouldAcceptServiceSignatureWithoutAttachedMethod()
    {
        $container = $this->mockContainer();
        $container->method('has')->with('myservice')->willReturn(true);

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

    private function mockContainer()
    {
        return $this->getMockbuilder('Interop\Container\ContainerInterface')->disableOriginalConstructor()->getMock();
    }
}
