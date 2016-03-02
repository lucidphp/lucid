<?php

namespace Lucid\Mux\Cache\Tests\Storage;

use Lucid\Mux\Cache\Storage\Memcached as Storage;

class MemcachedTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped('memcached extension not loaded.');
        }
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $store = new Storage(new \Memcached);
    }
}
