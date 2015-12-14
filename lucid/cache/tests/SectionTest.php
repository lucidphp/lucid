<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Tests;

use Lucid\Cache\Section;
use Lucid\Cache\Storage;

/**
 * @class SectionTest
 * @package Selene\Module\Cache\Tests
 * @version $Id$
 */
class SectionTest extends \PHPUnit_Framework_TestCase
{
    protected $cache;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Cache\SectionableInterface', new Section($this->mockStorage(), 'section'));
    }

    /** @test */
    public function itShouldReturnSection()
    {
        $this->assertInstanceof(
            'Lucid\Cache\Section',
            $this->newSection()->section('test')
        );
    }

    /** @test */
    public function itShouldSetValue()
    {
        $section = $this->newSection();
        $this->cache->method('get')->with('section:section:key')->willReturn('rnd');
        $this->cache->method('set')->with('rnd:section:key', 'data', '', false)->willReturn(true);

        $this->assertTrue($section->set('key', 'data'));
    }

    /** @test */
    public function itShouldIncrement()
    {
        $section = $this->newSection();
        $this->cache->method('get')->with('section:section:key')->willReturn('rnd');
        $this->cache->method('increment')->with('rnd:section:key', 1)->willReturn(1);

        $this->assertSame(1, $section->increment('key'));
    }

    /** @test */
    public function itShouldDecrement()
    {
        $section = $this->newSection();
        $this->cache->method('get')->with('section:section:key')->willReturn('rnd');
        $this->cache->method('decrement')->with('rnd:section:key', 1)->willReturn(1);

        $this->assertSame(1, $section->decrement('key'));
    }

    /**
     * @test
     * @dataProvider cmpProvider
     */
    public function itShouldSeal($compress)
    {
        $section = $this->newSection();
        $this->cache->method('get')->with('section:section:key')->willReturn('rnd');
        $this->cache->method('seal')->with('rnd:section:key', 'data', $compress)->willReturn(true);

        $this->assertTrue($section->persist('key', 'data', $compress));
    }

    /**
     * @test
     * @dataProvider cmpProvider
     */
    public function itShouldSealDefault($compress)
    {
        $callback = function () {
            return 'data';
        };

        $section = $this->newSection();
        $this->cache->method('get')->with('section:section:key')->willReturn('rnd');
        $this->cache->method('seal')->with('rnd:section:key', 'data', $compress)->willReturn(true);

        $this->assertTrue($section->persistUsing('key', $callback, $compress));
    }

    public function cmpProvider()
    {
        return [
            [true],
            [false],
        ];
    }

    protected function newSection($name = 'section')
    {
        $this->cache = $this->mockStorage();

        return new Section($this->cache, $name);
    }

    protected function mockStorage()
    {
        return $this->getMockbuilder('\Lucid\Cache\CacheInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    //protected function setUp()
    //{
        //$this->markTestIncomplete('replace mockery first');
    //}
}
