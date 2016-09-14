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
use Lucid\Signal\EventName;
use Lucid\Signal\EventInterface;

/**
 * @class EventNameTest
 *
 * @package Lucid\Signal\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventNameTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldParseEventName()
    {
        $name = new EventName($e = new Event);

        $this->assertSame('event', (string)$name);

        $name = new EventName($e = new Event('tata'));

        $this->assertSame('tata', (string)$name);

        $event = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->setMockClassName('MyGoofyEvent')
            ->getMock();

        $name = new EventName($event, null);

        $event->method('getOriginalName')->willReturn($name);

        $this->assertSame('my.goofy.event', (string)$name);
    }

    /** @test */
    public function itShouldNotParseEventNameIfNameExists()
    {
        $event = new Event;
        $event->setName('my_event');
        $name = new EventName($event);

        $this->assertSame('my_event', (string)$name);
    }
}
