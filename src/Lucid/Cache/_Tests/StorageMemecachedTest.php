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

use Memcached;
use Selene\Module\Cache\Storage;
use Selene\Module\Cache\Driver\MemcachedDriver;
use Selene\Module\Cache\Driver\MemcachedConnection;

/**
 * Class: StorageMemcachedTest
 *
 * @see \TestCases
 */
class StorageMemcachedTest extends StorageTestCase
{
    protected $memcached;

    protected $connection;

    /**
     * setUp
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped('Environment doesn\'t support Memcached');
        }

        $servers = [
            [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 100
            ]
        ];

        $connection = new MemcachedConnection($servers, new Memcached('memcached_test'));
        $connection->connect();

        $this->connection = $connection;
        $this->cache = new Storage(new MemcachedDriver($connection->getDriverAndConnect()), 'mycache');
    }

    /**
     * tearDown
     *
     * @access protected
     * @return void
     */
    protected function tearDown()
    {
        //$this->cache->purge();
        parent::tearDown();

        if ($this->connection) {
            $this->connection->close();
        }
    }
}
