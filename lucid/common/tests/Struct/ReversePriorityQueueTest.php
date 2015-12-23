<?php

/*
 * This File is part of the Lucid\Common package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Tests\Struct;

use Lucid\Common\Struct\ReversePriorityQueue;

/**
 * @class ReversePriorityQueueTest
 *
 * @package Lucid\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ReversePriorityQueueTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('SplPriorityQueue', new ReversePriorityQueue);
    }

    /** @test */
    public function itShouldPriorizeFromLowToHigh()
    {
        $q = new ReversePriorityQueue;

        $q->insert('bar', 100);
        $q->insert('foo', 10);

        $this->assertSame('foo', $q->extract());
        $this->assertSame('bar', $q->extract());
    }
}
