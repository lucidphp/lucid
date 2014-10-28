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

/**
 * @class EventTest
 *
 * @package Lucid\Module\Event\Tests
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
    }

    /** @test */
    public function aNameShouldBeSettable()
    {
        $event = $this->newEvent();
        $this->assertNull($event->getName());

        $event->setName('event');
        $this->assertSame('event', $event->getName());
    }

    protected function newEvent()
    {
        return new Event;
    }
}
