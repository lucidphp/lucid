<?php

/**
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Tests;

use Lucid\Resource\FileResource;

/**
 * @class FileResourceTest
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FileResourceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Resource\ResourceInterface', new FileResource(''));
    }

    /** @test */
    public function itShouldReturnResourcePathAsString()
    {
        $resource = new FileResource(__FILE__);

        $this->assertSame(__FILE__, $resource->getResource());
    }

    /** @test */
    public function itShouldBeLocalAResource()
    {
        $resource = new FileResource(__FILE__);

        $this->assertTrue($resource->isLocal());
    }

    /** @test */
    public function itShouldNotBeLocalAResource()
    {
        $resource = new FileResource('https://example.com/files/resource.txt');

        $this->assertFalse($resource->isLocal());
    }

    /** @test */
    public function itShouldBeAValidResource()
    {
        $resource = new FileResource(__FILE__);

        $this->assertTrue($resource->isValid(time()));
        $this->assertFalse($resource->isValid(0));
    }

    /** @test */
    public function itShouldNotBeValidIfResourceIsMissing()
    {
        $resource = new FileResource(__FILE__.'.inc');
        $this->assertFalse($resource->isValid(time()));
    }

    /** @test */
    public function itShouldNotBeAValidResource()
    {
        $resource = new FileResource(__FILE__);
        $time = filemtime(__FILE__) - 1;

        $this->assertFalse($resource->isValid($time));
    }

    /** @test */
    public function itShouldBeSerializable()
    {
        $resource = new FileResource(__FILE__);

        $data = serialize($resource);
        $ret = unserialize($data);

        $this->assertSame(get_class($resource), get_class($ret));
    }
}
