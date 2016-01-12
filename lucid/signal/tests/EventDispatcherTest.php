<?php

/*
 * This File is part of the Lucid\Signal\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Signal\Tests;

use Lucid\Signal\Event;
use Lucid\Signal\EventDispatcher;
use Lucid\Signal\Tests\Stubs\SimpleSubscriber;

/**
 * @class EventDispatcherTest
 *
 * @package Lucid\Signal\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Signal\EventDispatcher', new EventDispatcher);
    }

    /** @test */
    public function itShouldAllowInvokableHandlers()
    {
        $invoked = false;

        $handler = $this->getMock('InvokableHandler', ['__invoke']);
        $handler->method('__invoke')->willReturnCallback(function () use (&$invoked) {
            $invoked = true;
        });

        $events = new EventDispatcher;
        $events->addHandler('event', $handler);

        $events->dispatch('event');

        $this->assertTrue($invoked);
    }

    /** @test */
    public function itShouldAddHandlers()
    {
        $handle = false;
        $events = new EventDispatcher;

        $events->addHandler('event', $handler = $this->mockHandler());

        $handler->method('handleEvent')->willReturnCallback(function () use (&$handle) {
            $handle = true;
        });

        $events->dispatch('event');

        $this->assertTrue($handle);
    }

    /** @test */
    public function itShouldDispatchEventHandlersInOrder()
    {
        $order = [];
        $events = new EventDispatcher;

        $events->addHandler('event', $handlerA = $this->mockHandler(), 1);
        $events->addHandler('event', $handlerB = $this->mockHandler(), 10);
        $events->addHandler('event', $handlerC = $this->mockHandler(), 0);

        $handlerA->method('handleEvent')->willReturnCallback(function () use (&$order) {
            $order[] = 'A';
        });
        $handlerB->method('handleEvent')->willReturnCallback(function () use (&$order) {
            $order[] = 'B';
        });
        $handlerC->method('handleEvent')->willReturnCallback(function () use (&$order) {
            $order[] = 'C';
        });

        $events->dispatch('event');

        $this->assertSame(['B', 'A', 'C'], $order);

    }

    /** @test */
    public function itShouldDispatchEvents()
    {
        $event = new Event('my_event');

        $event->setName('my_event');
        $events = new EventDispatcher;
        $events->addHandler('my_event', $handler = $this->mockHandler(), 1);

        $invoked = false;
        $handler->method('handleEvent')->willReturnCallback(function () use (&$invoked) {
            $invoked = true;
        });

        $events->dispatchEvents([$event]);

        $this->assertTrue($invoked);
    }

    /** @test */
    public function itShouldGetHandlers()
    {
        $events = new EventDispatcher;

        $events->addHandler('event', $a = $this->mockHandler(), 1);
        $events->addHandler('event', $b = $this->mockHandler(), 10);
        $events->addHandler('myevent', $c = $this->mockHandler(), 100);

        $this->assertSame([[$b, 'handleEvent'], [$a, 'handleEvent']], $events->getHandlers('event'));
        $this->assertSame([[$b, 'handleEvent'], [$a, 'handleEvent'], [$c, 'handleEvent']], $events->getHandlers());
    }

    /** @test */
    public function itShouldAddSubscribers()
    {
        $events = new EventDispatcher;

        $sj = new \stdClass;

        $sub = new SimpleSubscriber($sj);

        $events->addSubscriber($sub);

        $this->assertSame([[$sub, 'onA']], $events->getHandlers('eventA'));
        $this->assertSame([[$sub, 'onB']], $events->getHandlers('eventB'));
    }

    /** @test */
    public function itShouldRemoveSubscribers()
    {
        $events = new EventDispatcher;

        $events = new EventDispatcher;

        $sj = new \stdClass;

        $sub = new SimpleSubscriber($sj);

        $events->addSubscriber($sub);
        $events->removeSubscriber($sub);

        $this->assertSame([], $events->getHandlers('eventA'));
        $this->assertSame([], $events->getHandlers('eventB'));
    }

    /** @test */
    public function itShouldTrowOnInvalidHandler()
    {
        $events = new EventDispatcher;
        try {
            $events->addHandler('event', 'foo@bar');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid handler "foo@bar".', $e->getMessage());
            return;
        }

        $this->fail('Test should throw InvalidArgumentException.');
    }

    /** @test */
    public function itShouldInvokeHandlerMethod()
    {
        $called = false;

        $handler = $this->getMock('Lucid\Signal\HandlerInterface');
        $handler->method('handleEvent')->will($this->returnCallback(function () use (&$called) {
            $called = true;
        }));

        $events = new EventDispatcher;
        $events->addHandler('event', $handler);
        $events->dispatch('event');

        $this->assertTrue($called);
    }

    /** @test */
    public function itShouldRemoveHandler()
    {
        $order = [];
        $events = new EventDispatcher;

        $events->addHandler('event', $handlerA = $this->mockHandler(), 1);
        $events->addHandler('event', $handlerB = $this->mockHandler(), 10);
        $events->addHandler('event', $handlerC = $this->mockHandler(), 20);

        $handlerA->method('handleEvent')->willReturnCallback(function () use (&$order) {
            $order[] = 'A';
        });
        $handlerB->method('handleEvent')->willReturnCallback(function () use (&$order) {
            $order[] = 'B';
        });
        $handlerC->method('handleEvent')->willReturnCallback(function () use (&$order) {
            $order[] = 'C';
        });

        $events->removeHandler('event', $handlerA);

        $events->dispatch('event');

        $this->assertSame(['C', 'B'], $order);

        $order = [];
        $events->removeHandler('event');
        $events->dispatch('event');

        $this->assertSame([], $order);
    }

    /** @test */
    public function itShouldStopDispatchingIfEventIsStopped()
    {
        $event = new Event('IAmStopped');

        $events = new EventDispatcher;

        $events->addHandler('IAmStopped', function ($event) {
            $event->stop();
        });

        $events->addHandler('IAmStopped', function ($event) {
            $this->fail('Handler should never been called.');
        });

        $events->dispatchEvent($event);
        $this->assertTrue(true);
    }

    /** @test */
    public function itShouldSetDispatcherOnChainedEvents()
    {
        $event = $this->getMockbuilder('Lucid\Signal\ChainedEventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())->method('setDispatcher');
        $event->method('getName')->willReturn('chained_event');

        $events = new EventDispatcher;
        $events->dispatchEvent($event);
    }

    private function mockHandler()
    {
        return $this->getMockbuilder('Lucid\Signal\HandlerInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
