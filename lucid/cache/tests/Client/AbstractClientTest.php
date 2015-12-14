<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Tests\Client;

/**
 * @class ClientTest
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractClientTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Lucid\Cache\ClientInterface',
            $this->newClient()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowOnInvalidDateFormat()
    {
        $driver = $this->newClient();

        $driver->parseExpireTime('foo bar');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowIfIncrementsIsString()
    {
        $driver = $this->newClient();

        $driver->increment('item.fails', '12');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowIfIncrementsIsInvalid()
    {
        $driver = $this->newClient();

        $driver->increment('item.fails', -1);
    }

    /** @test */
    public function itShouldReturnFalseIfItemDoesNotExist()
    {
        $driver = $this->newClient();

        $this->assertFalse($driver->exists('item.fails'));
    }

    /** @test */
    public function itShouldReturnTrueIfItemExists()
    {
        $this->assertTrue($this->newClient()->exists('item.exists'));
    }

    /** @test */
    public function itShouldFetchStoredItems()
    {
        $this->assertSame('exists', $this->newClient()->read('item.exists'));
    }

    /** @test */
    public function itShouldReturnNullIfItemDoesNotExist()
    {
        $this->assertNull($this->newClient()->read('item.fails'));
    }

    /**
     * @test
     * @dataProvider TimeProvider
     */
    public function itShouldReturnBooleanWhenStoringItems($time)
    {
        $driver = $this->newClient();

        $this->assertTrue($driver->write('item.success', 'data', $time));
        $this->assertFalse($driver->write('item.fails', 'data', $time));
    }

    /** @test */
    public function itShouldReturnBooleanWhenDeletingItems()
    {
        $driver = $this->newClient();

        $this->assertTrue($driver->delete('item.success'));
        $this->assertFalse($driver->delete('item.fails'));
    }

    /** @test */
    public function itShouldReturnIncrementedValue()
    {
        $driver = $this->newClient();

        $this->assertSame(2, $driver->increment('item.inc', 1));
        $this->assertFalse($driver->increment('item.fails', 1));
    }

    /** @test */
    public function itShouldReturnDecrementedValue()
    {
        $driver = $this->newClient();

        $this->assertSame(0, $driver->decrement('item.dec', 1));
        $this->assertFalse($driver->decrement('item.fails', 1));
    }

    /** @test */
    abstract public function flushingCacheShouldReturnBoolean();

    abstract protected function newClient();
}
