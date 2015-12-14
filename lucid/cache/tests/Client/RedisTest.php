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

use Lucid\Cache\CacheInterface;
use Lucid\Cache\Client\Redis as RedisClient;

/**
 * @class RedisClientTest
 *
 * @package Lucid\Cache\Tests\Client
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RedisTest extends AbstractClientTest
{
    protected $rd;
    protected $driver;

    /** @test */
    public function itShouldParseMinutesToUnixTimestamp()
    {
        $driver = $this->newClient();

        $this->assertSame(time() + 60, $driver->parseExpireTime(1));
    }

    /** @test */
    public function itShouldParseDateToUnixTimestamp()
    {
        $driver = $this->newClient();

        $this->assertSame(time() + 60, $driver->parseExpireTime('60 seconds'));
    }

    /** @test */
    public function flushingCacheShouldReturnBoolean()
    {
        $this->assertTrue($this->newClient()->flush());
    }

    /** @test */
    public function persistingShouldReturnBoolean()
    {
        $driver = $this->newClient();

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

    protected function newClient()
    {
        return $this->driver = new RedisClient($this->getRedis());
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
