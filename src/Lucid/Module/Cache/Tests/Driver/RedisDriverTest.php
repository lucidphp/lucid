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

use Lucid\Module\Cache\CacheInterface;
use Lucid\Module\Cache\Driver\RedisDriver;

/**
 * @class RedisDriverTest
 *
 * @package Lucid\Module\Cache\Tests\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RedisDriverTest extends DriverTest
{
    protected $rd;
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
    public function flushingCacheShouldReturnBoolean()
    {
        $this->assertTrue($this->newDriver()->flush());
    }

    /** @test */
    public function persistingShouldReturnBoolean()
    {
        $driver = $this->newDriver();

        $this->assertTrue($driver->write('item.success', 'data', CacheInterface::PERSIST));
        $this->assertTrue($driver->saveforever('item.success', 'data'));

        $this->assertFalse($driver->write('item.fails', 'data', CacheInterface::PERSIST));
        $this->assertFalse($driver->saveforever('item.fails', 'data'));
    }

    public function timeProvider()
    {
        return [
            [time() + 60]
        ];
    }

    protected function newDriver()
    {
        return $this->driver = new RedisDriver($this->getRedis());
    }

    protected function getRedis()
    {
        $mock = $this->rd = $this->getMock(
            'Redis',
            ['get', 'set', 'persist', 'expireAt', 'delete', 'flushAll', 'incrBy', 'decrBy']
        );

        $get = [
            ['item.fails', false],
            ['item.exists', serialize('exists')]
        ];

        $set = [
            ['item.fails', serialize('data'), false],
            ['item.success', serialize('data'), true]
        ];

        $delete = [
            ['item.fails', false],
            ['item.success', true]
        ];

        $increment = [
            ['item.inc', 1, 2],
            ['item.fails', 1, false]
        ];

        $decrement = [
            ['item.dec', 1, 0],
            ['item.fails', 1, false]
        ];

        $mock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($get));

        $mock->expects($this->any())
            ->method('set')
            ->will($this->returnValueMap($set));

        $mock->expects($this->any())
            ->method('delete')
            ->will($this->returnValueMap($delete));

        $mock->expects($this->any())
            ->method('incrBy')
            ->will($this->returnValueMap($increment));

        $mock->expects($this->any())
            ->method('decrBy')
            ->will($this->returnValueMap($decrement));

        $mock->expects($this->any())
            ->method('flushAll')
            ->willReturn(true);

        return $mock;
    }
}
