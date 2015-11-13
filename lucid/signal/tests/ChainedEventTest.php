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

        $d = $this->getMockBuilder('Lucid\Signal\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $event->setDispatcher($d);

        $this->assertTrue($d === $event->getDispatcher());
    }

    protected function newEvent()
    {
        return new ChainedEvent;
    }
}
