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
    protected $ordered;

    /** @test */
    public function itShouldAddHandlers()
    {
        $events = new EventDispatcher;

        $events->addHandler('event', [$this, 'fakeHandlerA']);

        $events->dispatch('event');

        $this->assertSame(['A'], $this->ordered);
    }

    /** @test */
    public function itShouldExecuteInOrder()
    {

        $events = new EventDispatcher;

        $events->addHandler('event', [$this, 'fakeHandlerA'], 1);
        $events->addHandler('event', [$this, 'fakeHandlerB'], 10);
        $events->addHandler('event', [$this, 'fakeHandlerC'], 0);

        $events->dispatch('event');

        $this->assertSame(['B', 'A', 'C'], $this->ordered);

    }

    /** @test */
    public function itShouldDispatchEvents()
    {
        $event = new Event;
        $event->setName('my_event');

        $events = new EventDispatcher;
        $events->addHandler('my_event', [$this, 'fakeHandlerA'], 1);

        $events->dispatchEvents([$event]);

        $this->assertSame(['A'], $this->ordered);
    }

    /** @test */
    public function itShouldGetHandlers()
    {
        $events = new EventDispatcher;

        $events->addHandler(
            'event',
            $a = function () {
            },
            1
        );

        $events->addHandler(
            'event',
            $b = function () {
            },
            10
        );

        $events->addHandler(
            'myevent',
            $c = function () {
            },
            100
        );

        $this->assertSame([$b, $a], $events->getHandlers('event'));
        $this->assertSame([$a, $b, $c], $events->getHandlers());
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
        $events = new EventDispatcher;

        $events->addHandler('event', [$this, 'fakeHandlerA'], 1);
        $events->addHandler('event', [$this, 'fakeHandlerB'], 10);
        $events->addHandler('event', [$this, 'fakeHandlerC'], 20);

        $events->removeHandler('event', [$this, 'fakeHandlerA']);

        $events->dispatch('event');

        $this->assertSame(['C', 'B'], $this->ordered);

        $this->ordered = [];
        $events->removeHandler('event');
        $events->dispatch('event');
        $this->assertSame([], $this->ordered);
    }

    public function fakeHandlerA()
    {
        $this->ordered[] = 'A';
    }

    public function fakeHandlerB()
    {
        $this->ordered[] = 'B';
    }

    public function fakeHandlerC()
    {
        $this->ordered[] = 'C';
    }

    protected function setUp()
    {
        $this->ordered = [];
    }
}
