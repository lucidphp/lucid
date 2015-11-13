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

/**
 * @class EventTest
 *
 * @package Lucid\Signal\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeStoppable()
    {
        $event = $this->newEvent();

        $this->assertFalse($event->isStopped());

        $event->stop();
        $this->assertTrue($event->isStopped());

        $clone = clone $event;
        $this->assertFalse($clone->isStopped());
    }

    /** @test */
    public function aNameShouldBeSettable()
    {
        $event = $this->newEvent();
        $this->assertInstanceOf('Lucid\Signal\EventName', $event->getName());

        $event->setName('event');
        $this->assertSame('event', (string)$event->getName());
    }

    protected function newEvent()
    {
        return new Event;
    }
}
