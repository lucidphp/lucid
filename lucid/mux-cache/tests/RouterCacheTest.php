<?php

namespace Lucid\Mux\Cache\Tests;

class RouterCacheTest extends \PHPUnit_Framework_TestCase
{
    private function mockStorage()
    {
        return $this->getMockbuilder('Lucid\Mux\Cache\StorateInterface')
            ->disableOriginalConstructor()->getMock();
    }
}
