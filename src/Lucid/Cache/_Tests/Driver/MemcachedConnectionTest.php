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

use \Selene\Module\Cache\Driver\MemcachedConnection as Connection;

class MemcachedConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\Cache\Driver\ConnectionInterface', new Connection);
    }

    /** @test */
    public function itShouldConnectToServer()
    {
        $conn = new Connection([['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]], new \Memcached);

        $this->assertFalse($conn->isConnected());

        $conn->connect();

        $this->assertTrue($conn->isConnected());

    }

    /** @test */
    public function itShouldCloseConnections()
    {
        $conn = new Connection([['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]], new \Memcached);

        $conn->connect();
        $this->assertTrue($conn->isConnected());

        if ($conn->close()) {
            $this->assertFalse($conn->isConnected());
        }
    }

    /** @test */
    public function itShouldThrowExceptionOnConnectionFailure()
    {
        $conn = new Connection([['host' => null, 'port' => 11211, 'weight' => 100]], $mc = new \Memcached);

        $mc->quit();

        try {
            $conn->connect();
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);

            return;
        }

        if ($conn->isConnected()) {
            $this->markTestSkipped();
        }

        $this->fail('oups');
    }
}
