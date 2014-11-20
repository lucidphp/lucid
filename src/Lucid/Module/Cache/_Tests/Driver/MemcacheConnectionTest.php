<?php

/**
 * This File is part of the Selene\Module\Cache\Tests\Driver package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Tests\Driver;

use \Selene\Module\Cache\Driver\MemcacheConnection;

/**
 * @class MemcacheConnectionTest
 * @package Selene\Module\Cache\Tests\Driver
 * @version $Id$
 */
class MemcacheConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\Cache\Driver\ConnectionInterface', new MemcacheConnection);
    }

    /** @test */
    public function itShouldConnectToServer()
    {
        $conn = new MemcacheConnection([['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]], new \Memcache);

        $this->assertFalse($conn->isConnected());

        $conn->connect();

        $this->assertTrue($conn->isConnected());

    }

    /** @test */
    public function itShouldCloseConnections()
    {
        $conn = new MemcacheConnection([['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]], new \Memcache);

        $conn->connect();
        $this->assertTrue($conn->isConnected());

        $conn->close();
        $this->assertFalse($conn->isConnected());
    }

    /** @test */
    public function itShouldReturnFalseIfAlreadyConnection()
    {
        $conn = new MemcacheConnection([['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]], new \Memcache);
        $this->assertTrue($conn->connect());
        $this->assertFalse($conn->connect());
    }

    /** @test */
    public function itShouldThrowExceptionOnConnectionFailure()
    {
        $conn = new MemcacheConnection([['host' => 'fakehost', 'port' => 11211, 'weight' => 100]], new \Memcache);
        try {
            $conn->connect();
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('oups');
    }
}
