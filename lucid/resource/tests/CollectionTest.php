<?php

namespace Lucid\Resource\Tests;

use Lucid\Resource\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\CollectionInterface', new Collection);
    }

    /** @test */
    public function itShouldReturnCollectionAsArray()
    {
        $resources = [
            $r1 = $this->mockResource(),
            $r2 = $this->mockResource(),
            $r3 = $this->mockResource(),
        ];

        $collection = new Collection($resources);

        $this->assertSame($resources, $collection->all());
    }


    /** @test */
    public function itSouldBeAbleToAddResources()
    {
        $res = $this->mockResource();

        $collection = new Collection;
        $collection->addResource($res);

        $this->assertSame([$res], $collection->all());
    }

    /** @test */
    public function itShouldBeAbleToAddFilePaths()
    {
        $collection = new Collection;

        $collection->addFileResource(__FILE__);

        $res = $collection->all();

        $this->assertArrayHasKey(0, $res);
        $this->assertInstanceOf('Lucid\Resource\FileResource', $res[0]);
    }

    /** @test */
    public function itShouldBeAbleToAddObjectResources()
    {
        $collection = new Collection;
        $collection->addObjectResource($this);

        $res = $collection->all();

        $this->assertArrayHasKey(0, $res);
        $this->assertInstanceOf('Lucid\Resource\ObjectResource', $res[0]);
    }

    private function mockResource()
    {
        return $this->getMockbuilder('Lucid\Resource\ResourceInterface')
            ->disableOriginalConstructor()->
            getMock();
    }
}
