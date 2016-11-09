<?php

/*
 * This File is part of the Lucid\Phlist package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Phlist\Tests;

use Lucid\Phlist\Phlist;
use InvalidArgumentException;

/**
 * @class PhlistTest
 *
 * @package Lucid\Phlist
 * @author iwyg <mail@thomas-appel.com>
 */
class PhlistTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function testPush()
    {
        $list = new Phlist(1, 2);
        $list->push(3);

        $this->assertSame([1, 2, 3], $list->toArray());
    }

    /** @test */
    public function itShouldHaveArrayAccess()
    {
        $list = new Phlist(1, 2);
        $list[] = 3;

        $this->assertSame([1, 2, 3], $list->toArray());

        unset($list[2]);

        $this->assertSame([1, 2], $list->toArray());
        $this->assertTrue(isset($list[0]));
        $this->assertTrue(isset($list[1]));
        $this->assertFalse(isset($list[2]));

        $this->assertSame(1, $list[0]);
        $this->assertSame(2, $list[1]);
    }

    /** @test */
    public function itShouldBeIteratable()
    {
        $list = new Phlist(1, 2, 3);
        $res = [];
        foreach ($list as $key => $value) {
            $res[$key] = $value;
        }

        $this->assertSame($res, $list->toArray());
    }

    /** @test */
    public function testConstructWithData()
    {
        $list = new Phlist(1, 2, 3, 4, 5);
        $this->assertEquals(5, count($list));
        $this->assertEquals([1, 2, 3, 4, 5], $list->toArray());
    }

    /** @test */
    public function testPop()
    {
        $list = new Phlist(1, 2, 3, 4, 5);

        $this->assertEquals(5, $list->pop());
        $this->assertEquals(2, $list->pop(1));
        $this->assertEquals(4, $list->pop(2));
    }

    /** @test */
    public function popShouldThrowErrorOnInvalidIndex()
    {
        $list = new Phlist(1, 2);

        try {
            $this->assertEquals(4, $list->remove(3));
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail();
    }

    /** @test */
    public function testInsert()
    {
        $list = new Phlist(1, 2, 3, 4, 5);

        $list->insert(3, 'foo');
        $this->assertEquals([1, 2, 3, 'foo', 4, 5], $list->toArray());
    }


    /** @test */
    public function testCountValue()
    {
        $list = new Phlist(1, 'red', 'green', 3, 'blue', 4, 'red', 5);

        $this->assertEquals(2, $list->countValue('red'));
        $this->assertEquals(1, $list->countValue('green'));
    }

    /** @test */
    public function testSort()
    {
        $list = new Phlist(120, -1, 3, 20, -110);

        $list->sort();

        $this->assertEquals([-110, -1, 3, 20, 120], $list->toArray());
    }

    /** @test */
    public function testRemove()
    {
        $list = new Phlist(1, 2, 3, 4, 5);

        $list->remove(3);

        $this->assertEquals([1, 2, 4, 5], $list->toArray());

        $list = new Phlist('red', 'green', 'blue');

        $list->remove('green');

        $this->assertEquals(['red', 'blue'], $list->toArray());
    }

    /** @test */
    public function testReverse()
    {
        $list = new Phlist(1, 2, 3, 4, 5);
        $list->reverse();

        $this->assertEquals([5, 4, 3, 2, 1], $list->toArray());
    }

    /** @test */
    public function testExtend()
    {
        $listA = new Phlist(1, 2, 3, 4, 5);
        $listB = new Phlist('red', 'green');

        $listA->extend($listB);

        $this->assertEquals([1, 2, 3, 4, 5, 'red', 'green'], $listA->toArray());
    }
}
