<?php

/**
 * This File is part of the Lucid\Http\Tests\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests\Request;

use Lucid\http\Request\FileInfo;

class FileInfoTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeImmutable()
    {
        $finfo = new FileInfo('file', 28, 'text/plain', 'somecypticstring', 0);

        try {
            $finfo->bar = 2;
        } catch (\Exception $e) {
            $this->assertInstanceof('LogicException', $e);
        }
    }
}
