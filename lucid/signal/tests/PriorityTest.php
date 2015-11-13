<?php

/**
 * This File is part of the lucid\signal\tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Signal\Tests;

use Lucid\Signal\Priority;

/**
 * @class PriorityTest
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Signal\PriorityInterface', new Priority);
    }

    /** @test */
    public function flushMustAlwaysReturnIterator()
    {
        $pri = new Priority;
        $this->assertInstanceOf('\Iterator', $pri->flush());
    }

    /** @test */
    public function itShouldFlushHandlersInCorrectOrder()
    {
        $handlerA = 'handlerA';
        $handlerB = 'handlerB';
        $handlerC = 'handlerC';

        $pri = new Priority;
        $pri->add($handlerA, -10);
        $pri->add($handlerB, 10);
        $pri->add($handlerC, 0);

        $handlers = [];

        foreach ($pri->flush() as $handler) {
            $handlers[] = $handler;
        }

        $this->assertSame(['handlerB', 'handlerC', 'handlerA'], $handlers);
    }
}
