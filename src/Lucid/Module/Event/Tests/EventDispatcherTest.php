<?php

/*
 * This File is part of the Lucid\Module\Event\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Event\Tests;

use Lucid\Module\Event\Event;
use Lucid\Module\Event\EventDispatcher;
use Lucid\Module\Event\Tests\Stubs\SimpleSubscriber;

/**
 * @class EventDispatcherTest
 *
 * @package Lucid\Module\Event\Tests
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
    public function itShouldAddSubscribers()
    {
        $events = new EventDispatcher;

        $sj = new \stdClass;
        $sj->first = '';
        $sj->second = '';

        $sub = new SimpleSubscriber($sj = new \stdClass);

        $events->addSubscriber($sub);

        $events->dispatch('eventA');
        $events->dispatch('eventB');

        $this->assertSame('A', $sj->first);
        $this->assertSame('B', $sj->second);
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
