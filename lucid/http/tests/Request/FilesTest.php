<?php

/*
 * This File is part of the Lucid\Http\Tests\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests\Request;

use Lucid\Http\Request\Files;

/**
 * @class FilesTest
 *
 * @package Lucid\Http\Tests\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $files = new Files([]);
    }

    /** @test */
    public function nonExistingPathShouldReturnNull()
    {
        $files = new Files([]);
        $this->assertNull($files->get('fail'));
    }

    /** @test */
    public function itShouldReturnInputArray()
    {
        $arr = ['files' => ['tmp_name' => [], 'name' => [], 'size' => []]];

        $files = new Files($arr);
        $this->assertSame($arr, $files->raw());
    }

    /** @test */
    public function itShouldFixSingleFilesArray()
    {
        $files = new Files($this->namedFileProvider());
        $res = $files->all();


        foreach (['file'] as $index) {
            //foreach (['error', 'name', 'size', 'tmp_name', 'type'] as $key) {
                //$this->assertTrue(
                    //isset($res['uploads']['file'][$key]),
                    //"['uploads']['file'][$key] should be set"
                //);
            //}
        }
    }

    /** @test */
    public function itShouldFixIndexedMultis()
    {
        $files = new Files($this->indexedMultiProvider());
        $res = $files->all();

        var_dump($files->all(true));
        $this->assertArrayHasKey('uploads', $res);
        $this->assertArrayHasKey('files', $res['uploads']);
        foreach ($res['uploads']['files'] as $file) {
            $this->assertInstanceof('Psr\Http\Message\UploadedFileInterface', $file);
        }

    }

    /** @test */
    public function itShouldFixNamedMulties()
    {
        $files = new Files($this->namedMultiProvider());
        $res = $files->all();
        $this->assertArrayHasKey('uploads', $res);

        foreach (['file1', 'file2'] as $index) {
            $this->assertArrayHasKey($index, $res['uploads']);
            $this->assertInstanceof('Psr\Http\Message\UploadedFileInterface', $res['uploads'][$index]);
        }
    }

    /** @test */
    public function itShouldFindFileByPath()
    {
        $files = new Files($this->namedMultiProvider());

        $this->assertInstanceof(
            'Psr\Http\Message\UploadedFileInterface',
            $files->get('uploads/file1')
        );
    }

    public function indexedMultiProvider()
    {
        return [
            'uploads' => [
                'files' => [
                    'name' => [
                        'fileA.txt',
                        'fileB.txt',
                    ],
                    'tmp_name' => [
                        '/tmp/tmpAname',
                        '/tmp/tmpBname',
                    ],
                    'size' => [
                        128,
                        64,
                    ],
                    'type' => [
                        'text/plain',
                        'text/plain'
                    ],
                    'error' => [
                        UPLOAD_ERR_OK,
                        UPLOAD_ERR_OK
                    ],
                ]
            ]
        ];
    }

    public function namedMultiProvider()
    {
        return [
            'uploads' => [
                'name' => [
                    'file1' => 'a.txt',
                    'file2' => 'b',
                ],
                'tmp_name' => [
                    'file1' => '/tmp/a',
                    'file2' => '/tmp/b',
                ],
                'size' => [
                    'file1' => 106,
                    'file2' => 224,
                ],
                'type' => [
                    'file1' => 'text/plain',
                    'file2' => 'application/octet-stream',
                ],
                'error' => [
                    'file1' => UPLOAD_ERR_OK,
                    'file2' => UPLOAD_ERR_OK,
                ],
            ]
        ];
    }

    public function namedFileProvider()
    {
        return [
            'uploads' => [
                'file' => [
                    'name' => 'file.txt',
                    'tmp_name' => '/tmp/path',
                    'size' => 28,
                    'type' => 'text/plain',
                    'error' => UPLOAD_ERR_OK,
                ]
            ]
        ];
    }
}
