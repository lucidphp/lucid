<?php

/*
 * This File is part of the Lucid\Event\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Event\Tests;

use Lucid\Event\ChainedEvent;

/**
 * @class ChainedEventTest
 *
 * @package Lucid\Event\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ChainedEventTest extends EventTest
{

    /** @test */
    public function aDispatcherShouldBeSettable()
    {
        $event = $this->newEvent();

        $event->setDispatcher($d = $this->getMock('Lucid\Event\EventDispatcherInterface'));

        $this->assertSame($d, $event->getDispatcher());
    }

    protected function newEvent()
    {
        return new ChainedEvent;
    }
}
