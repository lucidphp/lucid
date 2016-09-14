<?php

/*
 * This File is part of the Lucid\Signal\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

require __DIR__ .'/vendor/autoload.php';

use Lucid\Signal\Event;
use Lucid\Signal\EventName;

/**
 * @class EventNameTest
 *
 * @package Lucid\Signal\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventNameTest extends PHPUnit_Framework_TestCase
{
    public function testEventName()
    {
        $ev = new Event;
        $name = new EventName($ev);
        //$ev->getName();
        $name->getName();
    }

    public function testFooBar()
    {
        //$event = new Event;
        //$event->setName('my_event');
        //$name = new EventName($event);

        //$name->getName();
    }
}
