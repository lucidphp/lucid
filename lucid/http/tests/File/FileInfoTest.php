<?php

/**
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests\File;

use Lucid\Http\File\FileInfo;

/**
 * @class FileInfoTest
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FileInfoTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldDisallowUndefinedPropertiesToBeSet()
    {
        $finfo = new FileInfo('file', 28, 'text/plain', 'somecypticstring', 0);

        try {
            $finfo->bar = 2;
        } catch (\LogicException $e) {
            $this->assertSame('Can\'t set undefined property "bar".', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldGetFileProperties()
    {
        $finfo = new FileInfo('file', 28, 'text/plain', 'somecypticstring', 0);

        foreach (['name', 'tmpName', 'type', 'size', 'error'] as $prop) {
            $this->assertObjectHasAttribute($prop, $finfo);
        }
    }
}
