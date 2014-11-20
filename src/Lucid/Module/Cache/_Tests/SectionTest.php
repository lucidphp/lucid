<?php

/**
 * This File is part of the Selene\Module\Cache\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Tests;

use \Mockery as m;
use \Selene\Module\Cache\Section;
use \Selene\Module\TestSuite\TestCase;

/**
 * @class SectionTest
 * @package Selene\Module\Cache\Tests
 * @version $Id$
 */
class SectionTest extends TestCase
{
    protected $cache;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Module\Cache\CacheInterface', new Section($this->mockStorage(), 'section'));
    }

    /** @test */
    public function itShouldReturnSection()
    {
        $this->assertInstanceof(
            'Selene\Module\Cache\Section',
            $this->newSection()->section('test')
        );
    }

    /** @test */
    public function itShouldSetValue()
    {
        $section = $this->newSection();
        $this->cache->shouldReceive('get')->with('section:section:key')->andReturn('rnd');
        $this->cache->shouldReceive('set')->with('rnd:section:key', 'data', '', false)->andReturn(true);

        $this->assertTrue($section->set('key', 'data'));
    }

    /** @test */
    public function itShouldIncrement()
    {
        $section = $this->newSection();
        $this->cache->shouldReceive('get')->with('section:section:key')->andReturn('rnd');
        $this->cache->shouldReceive('increment')->with('rnd:section:key', 1)->andReturn(1);

        $this->assertSame(1, $section->increment('key'));
    }

    /** @test */
    public function itShouldDecrement()
    {
        $section = $this->newSection();
        $this->cache->shouldReceive('get')->with('section:section:key')->andReturn('rnd');
        $this->cache->shouldReceive('decrement')->with('rnd:section:key', 1)->andReturn(1);

        $this->assertSame(1, $section->decrement('key'));
    }

    /**
     * @test
     * @dataProvider cmpProvider
     */
    public function itShouldSeal($compress)
    {
        $section = $this->newSection();
        $this->cache->shouldReceive('get')->with('section:section:key')->andReturn('rnd');
        $this->cache->shouldReceive('seal')->with('rnd:section:key', 'data', $compress)->andReturn(true);

        $this->assertTrue($section->seal('key', 'data', $compress));
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
        $this->cache->shouldReceive('get')->with('section:section:key')->andReturn('rnd');
        $this->cache->shouldReceive('seal')->with('rnd:section:key', 'data', $compress)->andReturn(true);

        $this->assertTrue($section->sealDefault('key', $callback, $compress));
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
        return m::mock('Selene\Module\Cache\CacheInterface');
    }
}
