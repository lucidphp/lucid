<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Tests\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Tests\Driver;

/**
 * @class DriverTest
 *
 * @package Lucid\Module\Filesystem\Tests\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class DriverTest extends \PHPUnit_Framework_TestCase
{
    protected $stream;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Filesystem\Driver\DriverInterface', $this->newDriver());
    }

    /** @test */
    public function itShouldExists()
    {
        $driver = $this->newDriver();

        $this->needsFiles(['file.exists' => '']);
        $this->needsDirs(['dir_exists' => '']);

        $this->assertTrue($driver->exists('file.exists'));
        $this->assertTrue($driver->exists('dir_exists'));

        $this->assertFalse($driver->exists('file.fails'));
        $this->assertFalse($driver->exists('dir_fails'));
    }

    /** @test */
    public function itShouldBeAFile()
    {
        $this->needsFiles(['file.exists' => '']);

        $driver = $this->newDriver();

        $this->assertTrue($driver->isFile('file.exists'));
        $this->assertFalse($driver->isFile('file.fails'));
    }

    /** @test */
    public function itShouldBeADirectory()
    {
        $driver = $this->newDriver();

        $this->needsDirs(['dir_exists' => '']);

        $this->assertTrue($driver->isDir('dir_exists'));
        $this->assertFalse($driver->isDir('dir_fails'));
    }

    /** @test */
    public function itShouldCreateNewFile()
    {
        $driver = $this->newDriver();
        $this->assertInternalType('int', $res = $driver->writeFile('new.file', 'content'));
        $this->assertSame(mb_strlen('conetnt'), $res);
    }

    /** @test */
    public function itShouldUpdateFile()
    {
        $this->needsFiles(['file.exists' => 'content']);

        $driver = $this->newDriver();

        $this->assertInternalType('int', $res = $driver->updateFile('file.exists', 'new content'));
        $this->assertSame(mb_strlen('new conetnt'), $res);
    }

    /** @test */
    public function itShouldReadFileContents()
    {
        $this->needsFiles(['file.new' => 'content']);
        $driver = $this->newDriver();

        $this->assertInternalType('string', $res = $driver->readFile('file.new'));
        $this->assertSame('content', $res);
    }

    /** @test */
    public function itShouldReadFileparts()
    {
        $this->needsFiles(['file.new' => 'content']);
        $driver = $this->newDriver();

        $res = $driver->readFile('file.new', 1, 3);
        $this->assertSame('ont', $res);
    }

    /** @test */
    public function itShouldCreateADir()
    {
        $driver = $this->newDriver();

        $this->assertInternalType('boolean', $res = $driver->createDirectory('foo/bar', 0775, true));
        $this->assertTrue($res);
    }

    /** @test */
    public function itShouldFailCreatingDirectory()
    {
        $this->needsDirs(['foo/bar' => '']);
        $driver = $this->newDriver();

        $this->assertFalse($driver->createDirectory('foo/bar', 0775, true));
    }

    /** @test */
    public function itShouldFailReadingFile()
    {
        $driver = $this->newDriver();
        $this->assertFalse($driver->readFile('file.fails'));
    }

    /** @test */
    public function itShouldCopyAFile()
    {
        $driver = $this->newDriver();
        $this->needsFiles(['file.exists' => 'content']);

        $this->assertInternalType('int', $res = $driver->copyFile('file.exists', 'file.new'));
    }

    /** @test */
    public function itShouldFailCopyingAFile()
    {
        $driver = $this->newDriver();
        $this->assertFalse($driver->copyFile('file.exists', 'file.new'));
    }

    /** @test */
    public function itShouldRemoveADirRecursively()
    {
        $this->needsDirs([
            'dir_exists' => [
                'files' => ['test.txt' => ''],
                'dirs' => ['sub' => '']
            ]
        ]);

        $driver = $this->newDriver();
        $this->assertTrue($driver->deleteDirectory('dir_exists'));
    }

    /** @test */
    public function removeDirectoryShouldFail()
    {
        $driver = $this->newDriver();
        $this->assertFalse($driver->deleteDirectory('foo'));
    }

    /** @test */
    public function itShouldCopyDirectory()
    {
        $this->needsDirs([
            'dir_exists' => [
                'files' => ['test.txt' => ''],
                'dirs' => ['sub' => '']
            ]
        ]);

        $driver = $this->newDriver();
        $this->assertInternalType('integer', $driver->copyDirectory('dir_exists', 'dir_copy'));
    }

    /** @test */
    public function copyDirShouldFail()
    {
        $driver = $this->newDriver();
        $this->assertFalse($driver->copyDirectory('dir_exists', 'dir_copy'));
    }

    /** @test */
    public function itShouldListDirectory()
    {
        $this->needsDirs([
            'dir_exists' => [
                'files' => ['test.txt' => ''],
                'dirs' => ['sub' => '']
            ]
        ]);

        $driver = $this->newDriver();
        $this->assertInternalType('array', $list = $driver->listDirectory('.'));
        $this->assertArrayHasKey('dir_exists', $list);
        $this->assertFalse(isset($list['dir_exists/sub']));
    }

    /** @test */
    public function itShouldListDirectoryRecursively()
    {
        $this->needsDirs([
            'dir_exists' => [
                'files' => ['test.txt' => ''],
                'dirs' => ['sub' => '']
            ]
        ]);

        $driver = $this->newDriver();
        $this->assertInternalType('array', $list = $driver->listDirectory('.', true));

        $this->assertArrayHasKey('dir_exists', $list);
        $this->assertArrayHasKey('dir_exists/sub', $list);
        $this->assertArrayHasKey('dir_exists/test.txt', $list);
    }

    /** @test */
    public function itShouldWriteStream()
    {
        $driver = $this->newDriver();

        $stream = tmpfile();

        $res = $driver->writeStream('file.new', $this->newStream('content'));
        $this->assertInternalType('integer', $res);
    }

    /** @test */
    public function itShouldUpdateStream()
    {
        $this->needsFiles(['file.exists', 'content']);
        $driver = $this->newDriver();

        $res = $driver->updateStream('file.exists', $this->newStream('content new'));

        $this->assertInternalType('integer', $res);
    }

    /** @test */
    public function itShouldEnsureFile()
    {
        $driver = $this->newDriver();

        $this->assertInternalType('boolean', $res = $driver->ensureFile('new_dir/file.new'));
    }

    /** @test */
    public function itShouldEnsureDirectory()
    {
        $driver = $this->newDriver();

        $this->assertInternalType('boolean', $res = $driver->ensureDirectory('dir/sub'));
    }

    /** @test */
    public function itShouldRenameFile()
    {
        $this->needsFiles(['file.exists' => '']);
        $driver = $this->newDriver();

        $this->assertInternalType('boolean', $res = $driver->rename('file.exists', 'file.new'));
        $this->assertTrue($res);
    }

    /** @test */
    public function itShouldReturnPathInfo()
    {
        $this->needsFiles(['file.exists' => '']);

        $md = $this->newDriver($this->getMount())->getPathInfo('file.exists');

        $this->assertInternalType('array', $md);
    }

    protected function newStream($content = null)
    {
        $this->stream = tmpfile();
        if (null !== $content) {
            fputs($this->stream, $content);
        }

        return $this->stream;
    }

    protected function tearDown()
    {
        if (null !== $this->stream) {
            fclose($this->stream);
        }
    }

    abstract protected function newDriver($mount = null, array $options = []);

    abstract protected function getMount();

    abstract protected function needsFiles(array $files);

    abstract protected function needsDirs(array $dirs);
}
