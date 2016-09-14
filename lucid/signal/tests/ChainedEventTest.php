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

use Lucid\Signal\ChainedEvent;
use Lucid\Signal\EventDispatcherInterface;
use Lucid\Signal\EventDispatcher;

/**
 * @class ChainedEventTest
 *
 * @package Lucid\Signal\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ChainedEventTest extends EventTest
{
    /** @test */
    public function aDispatcherShouldBeSettable()
    {
        $event = $this->newEvent();

        $event->setDispatcher($d = new EventDispatcher());

        $this->assertTrue($d === $event->getDispatcher());
    }

    protected function newEvent()
    {
        return new ChainedEvent;
    }
}
