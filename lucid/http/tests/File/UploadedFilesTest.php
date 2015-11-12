<?php

/*
 * This File is part of the Lucid\Http\Tests\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests\File;

use Lucid\Http\File\UploadedFiles;

/**
 * @class FilesTest
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Http\File\UploadedFilesInterface', new UploadedFiles([]));
    }

    /**
     * @test
     * @dataProvider simpleMultiFileStruct
     */
    public function itShouldFixSimpleFileStructure($raw)
    {
        $files = new UploadedFiles($raw);

        $this->assertArrayHasKey('files', $all = $files->all());

        $this->assertSame(1, sizeof($all));
        $this->assertSame(2, sizeof($all['files']));

        $this->assertArrayHasKey(0, $all['files']);
        $this->assertArrayHasKey(1, $all['files']);

        foreach ($all['files'] as $file) {
            $this->assertInstanceOf('Psr\Http\Message\UploadedFileInterface', $file);
        }
    }

    /** @test */
    public function itShouldFixNamedFilesArray()
    {
        $files = new UploadedFiles($raw = include $this->fixure('files.02.php'));

        $this->assertSame($raw, $files->raw());
        $this->assertArrayHasKey('avatar', $all = $files->all());

        $this->assertInstanceOf('Psr\Http\Message\UploadedFileInterface', $all['avatar']);
    }

    /** @test */
    public function itShouldFixNamedNestedFilesArray()
    {
        $files = new UploadedFiles($raw = include $this->fixure('files.03.php'));

        $this->assertSame($raw, $files->raw());
        $all = $files->all();

        $this->assertTrue(isset($all['my-form']['details']['avatar']));
        $this->assertInstanceOf('Psr\Http\Message\UploadedFileInterface', $all['my-form']['details']['avatar']);
    }

    /** @test */
    public function itShouldFixNamedMultifilesArray()
    {
        $files = new UploadedFiles($raw = include $this->fixure('files.04.php'));

        $this->assertSame($raw, $files->raw());
        $all = $files->all();

        $this->assertTrue(isset($all['my-form']['details']['avatars']));
        $this->assertArrayHasKey(0, $all['my-form']['details']['avatars']);
        $this->assertArrayHasKey(1, $all['my-form']['details']['avatars']);

        $this->assertInstanceOf('Psr\Http\Message\UploadedFileInterface', $all['my-form']['details']['avatars'][0]);
        $this->assertInstanceOf('Psr\Http\Message\UploadedFileInterface', $all['my-form']['details']['avatars'][1]);
    }

    /**
     * @test
     * @dataProvider preSortedDataStruct
     */
    public function itShouldLeavePresortedDataUntouched($data)
    {
        $files = new UploadedFiles($data);

        $this->assertSame($data, $files->all());
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

    /**
     * Provides a simmple multipart files structure.
     *
     * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-7-http-message.md#16-uploaded-files
     * @example
     * Expected structure would be:
     *
     * ```php
     * array(
     *     'files' => array(
     *         0 => array(
     *             'name' => 'file0.txt',
     *             'type' => 'text/plain',
     *              //etc ...
     *         ),
     *         1 => array(
     *             'name' => 'file1.html',
     *             'type' => 'text/html',
     *              //etc ...
     *         ),
     *     ),
     * )
     * ```
     * @return array
     */
    public function simpleMultiFileStruct()
    {
        return [
            [
                [
                    'files' => [
                        'name' => [
                            'file0.txt',
                            'file1.html',
                        ],
                        'tmp_name' => [
                            '/var/tmp/gal9548kjhs',
                            '/var/tmp/0z5473euuLk',
                        ],
                        'type' => [
                            'text/plain',
                            'text/html',
                        ],
                        'size' => [
                            104,
                            92,
                        ],
                        'error' => [
                            UPLOAD_ERR_OK,
                            UPLOAD_ERR_OK
                        ],
                    ],
                ] ]
            ];
    }

    public function preSortedDataStruct()
    {
        return [
            [
                ['uploads' => [
                    'avatar' => $this->getMockbuilder('Psr\Http\Message\UploadedFileInterface')->disableOriginalConstructor()->getMock()
                ]]
            ]
        ];
    }


    /**
     * @param string $file
     *
     * @return string
     */
    private function fixure($file)
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Fixures' . DIRECTORY_SEPARATOR . trim($file, '\\/');
    }
}
