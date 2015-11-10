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
        $name = new EventName(new Event);

        $this->assertSame('event', (string)$name);
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
