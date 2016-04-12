<?php

namespace Lucid\Mux\Cache\Tests;

use Lucid\Mux\Cache\CacheLoader;

class CacheLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            'Lucid\Mux\Cache\CacheLoader',
            new CacheLoader('files', $this->mockStorage())
        );
    }

    private function mockStorage()
    {
        return $this->getMockbuilder('Lucid\Mux\Cache\StorageInterface')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockResource()
    {
        return $this->getMockbuilder('Lucid\Resource\ResourceInterface')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockResources()
    {
        return $this->getMockbuilder('Lucid\Resource\CollectionInterface')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockLoader()
    {
        return $this->getMockbuilder('Lucid\Resource\Loader\LoaderInterface')
            ->disableOriginalConstructor()->getMock();
    }
}
