<?php

/*
 * This File is part of the Lucid\Common\Tests\Struct package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Tests\Struct;

use Lucid\Common\Struct\Items;

/**
 * @class ItemsTest
 *
 * @package Lucid\Common\Tests\Struct
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ItemsTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function testConstructWithData()
    {
        $list = new Items(1, 2, 3, 4, 5);
        $this->assertEquals(5, count($list));
        $this->assertEquals([1, 2, 3, 4, 5], $list->toArray());
    }

    /** @test */
    public function testPop()
    {
        $list = new Items(1, 2, 3, 4, 5);

        $this->assertEquals(5, $list->pop());
        $this->assertEquals(2, $list->pop(1));
        $this->assertEquals(4, $list->pop(2));
    }

    /** @test */
    public function popShouldThrowErrorOnInvalidIndex()
    {
        $list = new Items(1, 2);

        try {
            $this->assertEquals(4, $list->remove(3));
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail();
    }

    /** @test */
    public function testInsert()
    {
        $list = new Items(1, 2, 3, 4, 5);

        $list->insert(3, 'foo');
        $this->assertEquals([1, 2, 3, 'foo', 4, 5], $list->toArray());
    }


    /** @test */
    public function testCountValue()
    {
        $list = new Items(1, 'red', 'green', 3, 'blue', 4, 'red', 5);

        $this->assertEquals(2, $list->countValue('red'));
        $this->assertEquals(1, $list->countValue('green'));
    }

    /** @test */
    public function testSort()
    {
        $list = new Items(120, -1, 3, 20, -110);

        $list->sort();

        $this->assertEquals([-110, -1, 3, 20, 120], $list->toArray());
    }

    /** @test */
    public function testRemove()
    {
        $list = new Items(1, 2, 3, 4, 5);

        $list->remove(3);

        $this->assertEquals([1, 2, 4, 5], $list->toArray());

        $list = new Items('red', 'green', 'blue');

        $list->remove('green');

        $this->assertEquals(['red', 'blue'], $list->toArray());
    }

    /** @test */
    public function testReverse()
    {
        $list = new Items(1, 2, 3, 4, 5);
        $list->reverse();

        $this->assertEquals([5, 4, 3, 2, 1], $list->toArray());
    }

    /** @test */
    public function testExtend()
    {
        $listA = new Items(1, 2, 3, 4, 5);
        $listB = new Items('red', 'green');

        $listA->extend($listB);

        $this->assertEquals([1, 2, 3, 4, 5, 'red', 'green'], $listA->toArray());
    }
}
