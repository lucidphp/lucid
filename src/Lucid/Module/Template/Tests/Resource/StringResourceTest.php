<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Tests\Resource;

use Lucid\Module\Template\Resource\StringResource;

/**
 * @class FileResourceTest
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StringResourceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Template\Resource\ResourceInterface', new StringResource('string'));
    }

    /** @test */
    public function itShouldGetPath()
    {
        $res = new StringResource('file');
        $this->assertSame(null, $res->getResource());
    }

    /** @test */
    public function itShouldContents()
    {
        $res = new StringResource('string');
        $this->assertSame('string', $res->getContents());
    }
}
