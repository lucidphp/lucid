<?php

/*
 * This File is part of the Lucid\Cache\Tests\Client package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Tests\Client;

use Memcache;
use Lucid\Cache\Client\Memcache as MemcacheClient;

/**
 * @class MemcachedClientTest
 *
 * @package Lucid\Cache\Tests\Client
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MemcacheTest extends MemcachedTest
{
    /** @test */
    public function itShouldReturnFalseIfItemDoesNotExist()
    {
        list (, $mc) = $this->getClient();

        $mc->method('get')->willReturn(false);

        return ClientTest::itShouldReturnFalseIfItemDoesNotExist();
    }

    /** @test */
    public function itShouldReturnNullIfItemDoesNotExist()
    {
        list (, $mc) = $this->getClient();
        $mc->method('get')->with('item.fails')->willReturn(false);

        return ClientTest::itShouldReturnNullIfItemDoesNotExist();
    }

    /**
     * @test
     * @dataProvider TimeProvider
     */
    public function itShouldReturnBooleanWhenStoringItems($time)
    {
        list (, $mc) = $this->getClient();
        $flags = 0 | Memcache::ITEM_EXISTS;
        $map = [
            ['item.success', 'data', $flags, $time, true],
            ['item.fails', 'data',$flags, $time, false]
        ];

        $mc->expects($this->any())
            ->method('set')
            ->will($this->returnValueMap($map));

        return ClientTest::itShouldReturnBooleanWhenStoringItems($time);
    }

    /** @test */
    public function itShouldReturnBooleanWhenDeletingItems()
    {
        list ($driver, $mc) = $this->getClient();

        $map = [
            ['item.success', true],
            ['item.fails', false]
        ];

        $mc->expects($this->any())
            ->method('delete')
            ->will($this->returnValueMap($map));

        return ClientTest::itShouldReturnBooleanWhenDeletingItems();
    }

    /** @test */
    public function itShouldReturnIncrementedValue()
    {
        list (, $mc) = $this->getClient();
        $map = [
            ['item.inc', 1, 2],
            ['item.fails', 1, false]
        ];

        $mc->expects($this->any())
            ->method('increment')
            ->will($this->returnValueMap($map));

        return ClientTest::itShouldReturnIncrementedValue();
    }

    /** @test */
    public function itShouldReturnDecrementedValue()
    {
        list (, $mc) = $this->getClient();
        $map = [
            ['item.dec', 1, 0],
            ['item.fails', 1, false]
        ];

        $mc->expects($this->any())
            ->method('decrement')
            ->will($this->returnValueMap($map));

        return ClientTest::itShouldReturnDecrementedValue();
    }

    protected function getMemcache()
    {
        $methods = ['get', 'set', 'delete', 'flush', 'increment', 'decrement'];

        return $this->getMock('Memcache', $methods);
    }

    protected function getClient()
    {
        if (null === $this->driver) {
            $this->driver = new Memcache($this->mc = $this->getMemcache());
        }

        return [$this->driver, $this->mc];
    }

    protected function newClient()
    {
        list($driver, ) = $this->getClient();

        return $driver;
    }

    protected function tearDown()
    {
        $this->mc = null;
        $this->driver = null;
    }
}
