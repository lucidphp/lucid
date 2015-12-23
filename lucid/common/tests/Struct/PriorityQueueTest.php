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

use Lucid\Common\Struct\PriorityQueue;

/**
 * @class PriorityQueueTest
 *
 * @package Lucid\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PriorityQueueTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('SplPriorityQueue', new PriorityQueue);
    }

    /** @test */
    public function itShouldPriorizeFromHighToLow()
    {
        $q = new PriorityQueue;

        $q->insert('foo', 10);
        $q->insert('bar', 100);

        $this->assertSame('bar', $q->extract());
        $this->assertSame('foo', $q->extract());
    }
}
