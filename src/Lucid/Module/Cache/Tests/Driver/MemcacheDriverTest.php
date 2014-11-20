<?php

/*
 * This File is part of the Lucid\Module\Cache\Tests\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Cache\Tests\Driver;

use Lucid\Module\Cache\Driver\MemcacheDriver;

/**
 * @class MemcachedDriverTest
 *
 * @package Lucid\Module\Cache\Tests\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MemcacheDriverTest extends MemcachedDriverTest
{
    /** @test */
    public function itShouldReturnFalseIfItemDoesNotExist()
    {
        list (, $mc) = $this->getDriver();

        $mc->method('get')->willReturn(false);

        return DriverTest::itShouldReturnFalseIfItemDoesNotExist();
    }

    /** @test */
    public function itShouldReturnNullIfItemDoesNotExist()
    {
        list (, $mc) = $this->getDriver();
        $mc->method('get')->with('item.fails')->willReturn(false);

        return DriverTest::itShouldReturnNullIfItemDoesNotExist();
    }

    /**
     * @test
     * @dataProvider TimeProvider
     */
    public function itShouldReturnBooleanWhenStoringItems($time)
    {
        list (, $mc) = $this->getDriver();
        $flags = 0 | MemcacheDriver::ITEM_EXISTS;
        $map = [
            ['item.success', 'data', $flags, $time, true],
            ['item.fails', 'data',$flags, $time, false]
        ];

        $mc->expects($this->any())
            ->method('set')
            ->will($this->returnValueMap($map));

        return DriverTest::itShouldReturnBooleanWhenStoringItems($time);
    }

    /** @test */
    public function itShouldReturnBooleanWhenDeletingItems()
    {
        list ($driver, $mc) = $this->getDriver();

        $map = [
            ['item.success', true],
            ['item.fails', false]
        ];

        $mc->expects($this->any())
            ->method('delete')
            ->will($this->returnValueMap($map));

        return DriverTest::itShouldReturnBooleanWhenDeletingItems();
    }

    /** @test */
    public function itShouldReturnIncrementedValue()
    {
        list (, $mc) = $this->getDriver();
        $map = [
            ['item.inc', 1, 2],
            ['item.fails', 1, false]
        ];

        $mc->expects($this->any())
            ->method('increment')
            ->will($this->returnValueMap($map));

        return DriverTest::itShouldReturnIncrementedValue();
    }

    /** @test */
    public function itShouldReturnDecrementedValue()
    {
        list (, $mc) = $this->getDriver();
        $map = [
            ['item.dec', 1, 0],
            ['item.fails', 1, false]
        ];

        $mc->expects($this->any())
            ->method('decrement')
            ->will($this->returnValueMap($map));

        return DriverTest::itShouldReturnDecrementedValue();
    }

    protected function getMemcache()
    {
        $methods = ['get', 'set', 'delete', 'flush', 'increment', 'decrement'];

        return $this->getMock('Memcache', $methods);
    }

    protected function getDriver()
    {
        if (null === $this->driver) {
            $this->driver = new MemcacheDriver($this->mc = $this->getMemcache());
        }

        return [$this->driver, $this->mc];
    }

    protected function newDriver()
    {
        list($driver, ) = $this->getDriver();

        return $driver;
    }

    protected function tearDown()
    {
        $this->mc = null;
        $this->driver = null;
    }
}
