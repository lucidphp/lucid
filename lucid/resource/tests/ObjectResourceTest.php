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

use Lucid\Resource\ObjectResource;

/**
 * @class ObjectResourceTest
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ObjectResourceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\ResourceInterface', new ObjectResource($this));
    }

    /** @test */
    public function itShouldGetObjectFilePath()
    {
        $resource = new ObjectResource($this);

        $this->assertSame(__FILE__, $resource->getResource());
    }

    /** @test */
    public function itShouldBeLocal()
    {
        $resource = new ObjectResource($this);

        $this->assertTrue($resource->isLocal());

        $resource = new ObjectResource($obj =  $this->getMock('ObjResourceMock'));
        $this->assertFalse($resource->isLocal());
    }

    /** @test */
    public function itShouldBeValid()
    {
        $resource = new ObjectResource($this);
        $this->assertTrue($resource->isValid(time()));
    }

    /** @test */
    public function itShouldBeInvalid()
    {
        $resource = new ObjectResource($this);
        $this->assertFalse($resource->isValid(filemtime(__FILE__) - 1));
    }

    /** @test */
    public function itShouldBeSerializable()
    {
        $resource = new ObjectResource($obj = $this);

        $data = serialize($resource);
        $ret = unserialize($data);

        $this->assertSame(get_class($resource), get_class($ret));
    }

    /** @test */
    public function itShouldThrowIfNotAnObject()
    {
        try {
            $resource = new ObjectResource('not an object.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('InvalidArgumentException should be thrown.');
    }

    /** @test */
    public function itShouldThrowIfInternalObject()
    {
        try {
            $resource = new ObjectResource(new \stdClass);
        } catch (\LogicException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('LogicException should be thrown.');
    }
}
