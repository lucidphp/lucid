<?php

/*
 * This File is part of the Lucid\Module\Template\Tests\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Tests\Resource;

use Lucid\Module\Template\Resource\FileResource;

/**
 * @class FileResourceTest
 *
 * @package Lucid\Module\Template\Tests\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FileResourceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Template\Resource\ResourceInterface', new FileResource('file'));
    }

    /** @test */
    public function itShouldGetPath()
    {
        $res = new FileResource('file');
        $this->assertSame('file', $res->getResource());
    }

    /** @test */
    public function itShouldContents()
    {
        $res = new FileResource(__FILE__);
        $this->assertStringEqualsFile(__FILE__, $res->getContents());
    }
}
