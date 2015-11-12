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
use Lucid\Http\File\UploadedFile;

class UploadedFileTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Psr\Http\Message\UploadedFileInterface', $this->newUploadedFile());
    }


    private function newUploadedFile(array $info = [])
    {
        if (empty($info)) {
            $info = $this->mockFileInfo();
        } else {
        }

        return new UploadedFile($info);
    }

    private function mockFileInfo()
    {
        return $this->getMockbuilder('Lucid\Http\File\FileInfo')->disableOriginalConstructor()->getMock();
    }
}
