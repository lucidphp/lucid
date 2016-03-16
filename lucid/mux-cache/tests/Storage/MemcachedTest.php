<?php

namespace Lucid\Mux\Cache\Tests\Storage;

use Lucid\Mux\Cache\Storage\Memcached as Storage;

class MemcachedTest extends \PHPUnit_Framework_TestCase
{
    use TestHelper;

    private $storeId = 'mux_cache_memcached_test';
    private $memcached;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\Cache\StorageInterface', new Storage(new \Memcached));
    }

    /** @test */
    public function itShouldWriteToAndReadFromCache()
    {
        $store = new Storage($this->memcached, $this->storeId);
        $store->write($this->mockRoutes());

        $this->assertInstanceOf('Lucid\Mux\Cache\CachedCollectionInterface', $store->read());
    }

    protected function setUp()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped('Memcached extension not loaded.');
        }

        $this->memcached = new \Memcached;
        if (!$this->memcached->addServers([['0.0.0.0', 11211]])) {
            $this->markTestSkipped('Cannot connect to memcached server.');
        }
    }

    protected function tearDown()
    {
        if (!extension_loaded('memcached')) {
            return;
        }

        $this->memcached->delete($this->storeId);
        $this->memcached->delete($this->storeId.'.lasmod');
        $this->memcached = null;
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $store = new Storage(new \Memcached);
    }
}
