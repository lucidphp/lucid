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

use Lucid\Module\Cache\Driver\MemcachedDriver;

/**
 * @class MemcachedDriverTest
 *
 * @package Lucid\Module\Cache\Tests\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MemcachedDriverTest extends DriverTest
{
    protected $mc;
    protected $driver;

    /** @test */
    public function itShouldParseMinutesToUnixTimestamp()
    {
        $driver = $this->newDriver();

        $this->assertSame(time() + 60, $driver->parseExpireTime(1));
    }

    /** @test */
    public function itShouldParseDateToUnixTimestamp()
    {
        $driver = $this->newDriver();

        $this->assertSame(time() + 60, $driver->parseExpireTime('60 seconds'));
    }

    /** @test */
    public function itShouldReturnFalseIfItemDoesNotExist()
    {
        list (, $mc) = $this->getDriver();

        $mc->method('get')->willReturn(false);
        $mc->method('getResultCode')->willReturn(\Memcached::RES_NOTFOUND);

        return parent::itShouldReturnFalseIfItemDoesNotExist();
    }

    /** @test */
    public function itShouldReturnTrueIfItemExists()
    {
        list (, $mc) = $this->getDriver();
        $mc->method('get')->willReturn(true);

        return parent::itShouldReturnTrueIfItemExists();
    }

    /** @test */
    public function itShouldFetchStoredItems()
    {
        list (, $mc) = $this->getDriver();
        $mc->method('get')->with('item.exists')->willReturn('exists');

        return parent::itShouldFetchStoredItems();
    }

    /** @test */
    public function itShouldReturnNullIfItemDoesNotExist()
    {
        list (, $mc) = $this->getDriver();
        $mc->method('get')->with('item.fails')->willReturn(false);
        $mc->method('getResultCode')->willReturn(\Memcached::RES_NOTFOUND);

        return parent::itShouldReturnNullIfItemDoesNotExist();
    }

    /**
     * @test
     * @dataProvider TimeProvider
     */
    public function itShouldReturnBooleanWhenStoringItems($time)
    {
        list (, $mc) = $this->getDriver();
        $map = [
            ['item.success', 'data', $time, true],
            ['item.fails', 'data', $time, false]
        ];

        $mc->expects($this->any())
            ->method('set')
            ->will($this->returnValueMap($map));

        return parent::itShouldReturnBooleanWhenStoringItems($time);
    }

    /** @test */
    public function itShouldReturnBooleanWhenDeletingItems()
    {
        list (, $mc) = $this->getDriver();

        $map = [
            ['item.success', null, true],
            ['item.fails', null, false]
        ];

        $mc->expects($this->any())
            ->method('delete')
            ->will($this->returnValueMap($map));

        return parent::itShouldReturnBooleanWhenDeletingItems();
    }

    /** @test */
    public function flushingCacheShouldReturnBoolean()
    {
        list ($driver, $mc) = $this->getDriver();
        $mc->method('flush')->willReturn(true);

        $this->assertTrue($driver->flush());

        $this->mc = null;
        $this->driver = null;

        list ($driver, $mc) = $this->getDriver();
        $driver = new MemcachedDriver($mc = $this->getMemcached());
        $mc->method('flush')->willReturn(false);

        $this->assertFalse($driver->flush());
    }

    /** @test */
    public function memcachedShouldReceiveZeroEpireytime()
    {
        $driver = new MemcachedDriver($mc = $this->getMemcached());
        $mc->method('set')->with('item.success', 'data', 0)->willReturn(true);

        $this->assertTrue($driver->saveForever('item.success', 'data'));
    }

    /** @test */
    public function itShouldReturnIncrementedValue()
    {
        list (, $mc) = $this->getDriver();
        $map = [
            ['item.inc', 1, null, null, 2],
            ['item.fails', 1, null, null, false]
        ];

        $mc->expects($this->any())
            ->method('increment')
            ->will($this->returnValueMap($map));

        return parent::itShouldReturnIncrementedValue();
    }

    /** @test */
    public function itShouldReturnDecrementedValue()
    {
        list (, $mc) = $this->getDriver();
        $map = [
            ['item.dec', 1, null, null, 0],
            ['item.fails', 1, null, null, false]
        ];

        $mc->expects($this->any())
            ->method('decrement')
            ->will($this->returnValueMap($map));

        return parent::itShouldReturnDecrementedValue();
    }

    public function timeProvider($time)
    {
        return [
            [time() + 60]
        ];
    }

    protected function getMemcached()
    {
        return $this->getMock('Memcached');
    }

    protected function getDriver()
    {
        if (null === $this->driver) {
            $this->driver = new MemcachedDriver($this->mc = $this->getMemcached());
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

    protected function setUp()
    {
        $this->mc = null;
        $this->driver = null;
    }
}
