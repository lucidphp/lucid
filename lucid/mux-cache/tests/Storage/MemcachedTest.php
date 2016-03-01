<?php

namespace Lucid\Mux\Cache\Tests\Storage;

use Lucid\Mux\Cache\Storage\Memcached as Storage;

class MemcachedTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $store = new Storage(new \Memcached);
    }
}
