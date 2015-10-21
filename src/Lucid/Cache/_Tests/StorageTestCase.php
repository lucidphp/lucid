<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Tests;

use StdClass;
use Selene\Module\TestSuite\TestCase;

/**
 * Class: TestCase
 *
 * @see \PHPUnit_Framework_TestCase
 * @abstract
 */
abstract class StorageTestCase extends TestCase
{

    protected $driver;
    /**
     * @test
     */
    public function testCacheReadShouldReturnNull()
    {
        //$this->assertTrue(is_null($this->cache->get('cached_null')));
    }

    /**
     * @test
     */
    public function testCacheObjects()
    {
        $object = new StdClass();
        $object->name = 'testobj';

        $this->cache->set('cached_obj', $object, 1000);

        $cachedObj = $this->cache->get('cached_obj');

        $this->assertEquals($object, $cachedObj);
    }

    /** @test */
    public function itShouldHaveArrayAccess()
    {
        $this->assertFalse(isset($this->cache['key']));
        $this->cache['key'] = 'data';
        $this->assertTrue(isset($this->cache['key']));

        $this->assertSame('data', $this->cache['key']);

        unset($this->cache['key']);

        $this->assertFalse(isset($this->cache['key']));
    }

    /**
     * @test
     */
    public function testCacheArrays()
    {
        $bar = 'bar';
        $array = array('foo' => &$bar);

        $this->cache->set('cached_array', $array, 1000);
        $cachedArray = $this->cache->get('cached_array');

        $this->assertSame($array, $cachedArray);
        $this->assertTrue($array === $cachedArray);
    }

    /**
     * @test
     */
    public function testCacheStrings()
    {
        $this->cache->set('cached_string', 'foobar', 1000);
        $this->assertSame('foobar', $this->cache->get('cached_string'));
    }

    /**
     * @test
     */
    public function testCacheWriteDefault()
    {
        $this->cache->setDefault(
            'cached_default',
            function () {
                return 'foobar';
            },
            1000
        );

        $this->assertSame('foobar', $this->cache->get('cached_default'));
    }


    /**
     * @test
     */
    public function testHasCachedItem()
    {
        $this->cache->set('cached_string', 'foobar', 1000);
        $this->assertTrue($this->cache->has('cached_string'));
    }

    public function testCompressWriteToCache()
    {
        $this->cache->set('cached_string', 'foobar', 1000, true);
        $this->assertSame('foobar', $this->cache->get('cached_string'));
    }

    /**
     * @test
     */
    public function testPurgeCache()
    {
        $this->cache->set('cached_string_1', 'foobarbazo', 1000);
        $this->cache->set('cached_string_2', 'foobarboom', 1000);
        $this->cache->set('cached_string_3', 'foobarbara', 1000);

        $this->assertTrue($this->cache->has('cached_string_3'));

        $this->cache->purge('cached_string_3');

        $this->assertFalse($this->cache->has('cached_string_3'));
        $this->assertTrue($this->cache->has('cached_string_1'));
        $this->assertTrue($this->cache->has('cached_string_2'));

        $this->cache->purge();
        $this->assertFalse($this->cache->has('cached_string_1'));
        $this->assertFalse($this->cache->has('cached_string_2'));
    }

    /**
     * @test
     */
    public function testIncrementValue()
    {
        $this->cache->set('foo.int', 1);
        $this->cache->increment('foo.int', 2);

        $this->assertEquals(3, $this->cache->get('foo.int'));
    }

    public function testCacheHasKey()
    {
        $this->assertFalse($this->cache->has('key.foo'));
        $this->cache->set('key.foo', 'bar');
        $this->assertTrue($this->cache->has('key.foo'));
    }
    /**
     * @test
     */
    public function testDecrementValue()
    {
        $this->cache->set('foo.int', 3);
        $this->cache->decrement('foo.int', 1);
        $this->assertEquals(2, $this->cache->get('foo.int'));
    }

    public function testSetSection()
    {
        $this->cache->section('names')->set('thomas', 'appel');
        $this->assertEquals('appel', $this->cache->section('names')->get('thomas'));
    }

    public function testSetPurge()
    {
        $this->cache->purge();
        $this->cache->section('names')->set('thomas', 'appel');
        $this->cache->section('names')->set('allen', 'smith');
        $this->assertEquals('appel', $this->cache->section('names')->get('thomas'));
        $this->assertEquals('smith', $this->cache->section('names')->get('allen'));
        $this->cache->section('names')->purge('thomas');

        $this->assertFalse($this->cache->section('names')->has('thomas'));

        $this->assertTrue($this->cache->section('names')->has('allen'));
        $this->cache->section('names')->purge();
        $this->assertFalse($this->cache->section('names')->has('allen'));
    }
}
