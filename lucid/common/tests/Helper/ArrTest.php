<?php

/*
 * This File is part of the Lucid\Common\Tests\Helper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Tests\Helper;

use Lucid\Common\Helper\Arr;

/**
 * @class ArrTest
 *
 * @package Lucid\Common\Tests\Helper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ArrTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function flatten()
    {
        $this->assertSame(['b' => 'c'], Arr::flatten(['a' => ['b' => 'c']]));
    }

    /** @test */
    public function column()
    {
        $in = [
            [
                'id' => '12',
                'name' => 'rand',
                'handle' => 'xkd23',
            ],
            [
                'id' => '14',
                'name' => 'band',
                'handle' => 'xkd25',
            ],
            [
                'id' => '22',
                'name' => 'land',
                'handle' => 'xkd77',
            ],
        ];

        $this->assertEquals(['12', '14', '22'], Arr::column($in, 'id'));
        $this->assertEquals(
            ['xkd23' => '12', 'xkd25' => '14', 'xkd77' => '22'],
            Arr::column($in, 'id', 'handle')
        );
    }

    /** @test */
    public function isList()
    {
        $this->assertTrue(Arr::isList([1, 2, 3]));
        $this->assertTrue(Arr::isList([0 => 1, 1 => 2, 2 => 3]));
        $this->assertFalse(Arr::isList(['a' => 1, 'b' => 2]));
        $this->assertFalse(Arr::isList([1 => 1, 2 => 2, 3 => 3], true));
        $this->assertFalse(Arr::isList(["0" => 1, "1" => 2, "bla" => 3], true));
    }

    /** @test */
    public function testMax()
    {
        $this->assertEquals(3, Arr::max([['a', 'b', 'c'], ['A', 'C'], [1, 2, 3]]));
    }

    /** @test */
    public function testMin()
    {
        $this->assertEquals(2, Arr::min([['a', 'b', 'c'], ['A', 'C'], [1, 2, 3]]));
    }

    /** @test */
    public function testArrayGetShouldReturnNullOnUnknowenKeys()
    {
        $this->assertNull(Arr::get(['foo' => ['bar' => 'baz']], 'baz.bar'));
        $this->assertNull(Arr::get(['foo' => ['bar' => 'baz']], 'fo.baz'));
    }

    /**
     * @test
     * @dataProvider arrayGetDataProvider
     */
    public function testArrayGet($query, $array, $expected)
    {
        $this->assertEquals($expected, Arr::get($array, $query));
    }

    /** @test */
    public function testArrayGetReturnInput()
    {
        $this->assertEquals(['a' => 'b'], Arr::get(['a' => 'b']));
        $this->assertEquals(['a' => 'b'], Arr::get(['a' => 'b']));
    }

    /** @test */
    public function set()
    {
        $array = [];
        Arr::set($array, 'foo', 'bar');
        Arr::set($array, 'service.location.locale', 'en');
        Arr::set($array, 'service.location.name', 'myservice');
        Arr::set($array, 'service.namespace', 'myserviceNS');
        Arr::set($array, 'service.location.0', 'in1');
        Arr::set($array, 'service.location.1', 'in2');

        $this->assertTrue(isset($array['foo']) && $array['foo'] === 'bar');
        $this->assertTrue(isset($array['service']));
        $this->assertTrue(
            isset($array['service']['namespace']) && $array['service']['namespace'] === 'myserviceNS'
        );
        $this->assertTrue(isset($array['service']['location']));
        $this->assertTrue(
            isset($array['service']['location']['locale']) && $array['service']['location']['locale'] === 'en'
        );
        $this->assertTrue(
            isset($array['service']['location']['name']) && $array['service']['location']['name'] === 'myservice'
        );

        $data = [];

        Arr::set($data, 'foo', 'bar');
        Arr::set($data, 'baz', ['doo']);
        Arr::set($data, 'baz.some', 'goo');
        Arr::set($data, 'baz.glue', 'fuxk');
    }

    public function arrayGetDataProvider()
    {
        return [
            [
                'foo.bar',
                ['foo' => ['bar' => 'baz']],
                'baz'
            ],
            [
                'foo.bar.baz',
                ['foo' => ['bar'=> ['baz' => 'boom']]],
                'boom'
            ],
            [
                'foo.bar.baz.boom',
                ['foo' => ['bar'=> ['baz' => ['boom' => 'baz']]]],
                'baz'
            ],
            [
                'foo.bar.0',
                ['foo' => ['bar' => [1, 2, 3]]],
                1
            ],
            [
                'foo.bar.1',
                ['foo' => ['bar' => [1, 2, 3]]],
                2
            ]
        ];
    }
}
