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
    public function allMustAlwaysReturnIterator()
    {
        $pri = new Priority;
        $this->assertInstanceOf('\Iterator', $pri->all());
    }

    /** @test */
    public function itShouldThrowExceptionOnInvalidHandlers()
    {
        $pri = new Priority;
        try {
            $pri->add(['not_a_handler'], 0);
        } catch (\RuntimeException $e) {
            $this->assertSame('Can\'t convert handler to string.', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldAlwaysReturnAnIteratable()
    {
        $pri = new Priority;
        $this->assertInstanceof('\Iterator', $pri->all());
    }


    /** @test */
    public function itShouldRemoveHandler()
    {
        $pri = new Priority;

        $pri->add('handlerA', 1);
        $pri->add('handlerC', 20);
        $pri->add('handlerB', 10);

        $pri->remove('handlerA');

        $this->assertSame(['handlerC', 'handlerB'], $pri->toArray());

        $pri->remove('c');
        $pri->remove('handlerC');

        $this->assertSame(['handlerB'], $pri->toArray());
    }

    /** @test */
    public function itShouldAllHandlersInCorrectOrder()
    {
        $handlerA = 'handlerA';
        $handlerB = 'handlerB';
        $handlerC = 'handlerC';

        $pri = new Priority;
        $pri->add($handlerA, -10);
        $pri->add($handlerB, 10);
        $pri->add($handlerC, 0);

        $handlers = [];

        foreach ($pri->all() as $handler) {
            $handlers[] = $handler;
        }

        $this->assertSame(['handlerB', 'handlerC', 'handlerA'], $handlers);
    }
}
