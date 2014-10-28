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

use Mockery as m;
use Lucid\Module\Event\ChainedEvent;

/**
 * @class ChainedEventTest
 *
 * @package Lucid\Module\Event\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ChainedEventTest extends EventTest
{

    /** @test */
    public function aDispatcherShouldBeSettable()
    {
        $event = $this->newEvent();

        $event->setDispatcher($d = m::mock('Lucid\Module\Event\EventDispatcherInterface'));

        $this->assertSame($d, $event->getDispatcher());
    }

    protected function tearDown()
    {
        m::close();
    }

    protected function newEvent()
    {
        return new ChainedEvent;
    }
}
