<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Tests;

use Lucid\Resource\Collection;

/**
 * @class CollectionTest
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\CollectionInterface', new Collection);
        $this->assertInstanceOf('Traversable', new Collection);
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
    public function itShouldBeTraversable()
    {
        $resources = [
            $r1 = $this->mockResource(),
            $r2 = $this->mockResource(),
            $r3 = $this->mockResource(),
        ];

        $c = new Collection($resources);

        $ret = [];
        foreach ($c as $i => $res) {
            $this->assertSame($i, $c->key());
            $ret[] = $res;
        }

        $this->assertSame($resources, $ret);
    }

    /** @test */
    public function itShouldTestValidity()
    {
        $time = time();

        $resources = [
            $r1 = $this->mockResource(),
            $r2 = $this->mockResource()
        ];

        $r2->method('isValid')->with($time)->willReturn(false);
        $r1->method('isValid')->with($time)->willReturn(true);

        $collection = new Collection($resources);

        $this->assertFalse($collection->isValid($time));

        $resources = [
            $r1 = $this->mockResource(),
            $r2 = $this->mockResource()
        ];

        $r2->method('isValid')->with($time)->willReturn(true);
        $r1->method('isValid')->with($time)->willReturn(true);

        $collection = new Collection($resources);
        $this->assertTrue($collection->isValid($time));
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
