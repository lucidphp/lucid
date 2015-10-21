<?php

/*
 * This File is part of the Lucid\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Tests;

use Lucid\Filesystem\Filesystem;
use Lucid\Filesystem\Exception\IOException;
use Lucid\Filesystem\Driver\DriverInterface;

/**
 * @class FilesystemTest
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            'Lucid\Filesystem\FilesystemInterface',
            new Filesystem
        );
    }

    /** @test */
    public function itShouldExists()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('exists')->willReturn(true);

        $this->assertTrue($fs->exists('path'));
    }

    /** @test */
    public function itShouldBeADir()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('isDir')->willReturn(true);

        $this->assertTrue($fs->isDir('path'));
    }

    /** @test */
    public function itShouldNotNeAFileOrLink()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('isFile')->willReturn(false);
        $driver->method('isLink')->willReturn(false);

        $this->assertFalse($fs->isFile('path'));
        $this->assertFalse($fs->isLink('path'));
    }

    /** @test */
    public function itShouldEnsureDir()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('ensureDirectory')->with('path')->willReturn(true);

        $this->assertTrue($fs->ensureDirectory('path'));
    }

    /** @test */
    public function ensureDirShouldFail()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('ensureDirectory')->with('path')->willReturn(false);

        $this->assertFalse($fs->ensureDirectory('path'));
    }

    /** @test */
    public function itShouldEnsureFile()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('ensureFile')->with('path')->willReturn(true);

        $this->assertTrue($fs->ensureFile('path'));
    }

    /** @test */
    public function ensureFileShouldFail()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('ensureFile')->with('path')->willReturn(false);

        $this->assertFalse($fs->ensureFile('path'));
    }

    /** @test */
    public function writingFileShouldFail()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('exists')->with('file.fails')->willReturn(false);
        $driver->method('isFile')->with('file.fails')->willReturn(false);

        try {
            $fs->writeFile('file.fails', '');
        } catch (IOException $e) {
            $this->assertSame('Cannot write to file "file.fails".', $e->getMessage());
        }
    }

    /** @test */
    public function writingFileShouldSucceed()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('exists')->with('file.new')->willReturn(false);
        $driver->method('writeFile')->with('file.new')->willReturn(0);

        $this->assertSame(0, $fs->writeFile('file.new', ''));
    }

    /** @test */
    public function updatingFileShouldSucceed()
    {
        $fs = $this->newFs($driver = $this->mockDriver());
        $driver->method('exists')->with('file.exists')->willReturn(true);
        $driver->method('isFile')->with('file.exists')->willReturn(true);
        $driver->method('updateFile')->with('file.exists')->willReturn(4);

        $this->assertSame(4, $fs->writeFile('file.exists', 'test'));
    }

    /**
     * @test
     * @dataProvider enumProvider
     */
    public function itShouldEnumFileName($path, $newName, $map)
    {
        $fs = $this->newFs($driver = $this->mockDriver());

        $driver->expects($this->any())
            ->method('listDirectory')
            ->will($this->returnValueMap($map));

        $this->assertSame($newName, $fs->enum($path));
    }

    public function enumProvider()
    {
        return [
            [
                'dir/file.txt',
                'dir/file copy.txt',
                [['dir', $this->mockList(['file.txt'])]]
            ],
            [
                'dir/pathname',
                'dir/pathname copy',
                [['dir', $this->mockList(['pathname'])]]
            ],
            [
                'dir/pathname',
                'dir/pathname copy 2',
                [['dir', $this->mockList(['pathname', 'pathname copy', 'pathname copy 1'])]]
            ]
        ];
    }

    /**
     * newFs
     *
     * @param DriverInterface $driver
     *
     * @return void
     */
    protected function newFs(DriverInterface $driver = null)
    {
        return new Filesystem($driver);
    }

    /**
     * mockDriver
     *
     *
     * @return void
     */
    protected function mockDriver()
    {
        return $this->getMock('Lucid\Filesystem\Driver\DriverInterface');
    }

    /**
     * mockList
     *
     * @param array $files
     * @param array $dirs
     *
     * @return void
     */
    protected function mockList(array $files = [], array $dirs = [])
    {
        $out = [];

        foreach ($files as $file) {
            $out[$file] = [
                'path' => $file,
                'size' => strlen($file),
            ];
        }

        foreach ($dirs as $dir) {
            $out[$dir] = [
                'path' => $dir
            ];
        }

        return $out;
    }
}
