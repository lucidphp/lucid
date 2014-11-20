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

use Lucid\Module\Filesystem\Filesystem;
use Lucid\Module\Filesystem\Driver\LocalDriver;

/**
 * @class LocalDriverTest
 *
 * @package Lucid\Module\Filesystem\Tests\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LocalDriverTest extends DriverTest
{
    protected $testDrive;

    /**
     * {@inheritdoc}
     */
    protected function newDriver($mount = null, array $options = [])
    {
        $driver = new LocalDriver($mount ?: $this->getMount(), $options);

        return $driver;
    }

    /**
     * {@inheritdoc}
     */
    protected function getMount()
    {
        return $this->getTestDrive();
    }


    ///** @test */
    //public function itShouldCheckForDirs()
    //{
    //    mkdir($td = $this->getTestDrive() . '/testmount', 0777, true);

    //    $driver = new LocalDriver($this->getTestDrive());

    //    $this->assertTrue($driver->isDir('testmount'));
    //    $this->assertTrue($driver->exists('testmount'));
    //    $this->assertFalse($driver->isFile('testmount'));
    //}

    ///** @test */
    //public function itShouldMountOnMountPoints()
    //{
    //    mkdir(($td = $this->getTestDrive()) . '/testmount/some/dir', 0775, true);

    //    $driver = new LocalDriver($td.'/testmount');

    //    $res = $driver->createDirectory('some/bar', 0755, true);

    //    //var_dump($res);
    //}

    ///** @test */
    //public function itShouldCopyFile()
    //{

    //    mkdir($td = $this->getTestDrive() . '/testmount', 0777, true);

    //    touch($file = $td.'/testfile');
    //    file_put_contents($file, 'some content');

    //    $driver = new LocalDriver($td);

    //    $res = $driver->copyFile('testfile', 'copyfile');


    //    //var_dump($res);
    //}

    ///** @test */
    //public function itShouldCopyDirectories()
    //{
    //    mkdir($dir = ($td = $this->getTestDrive() . '/testmount').'/subdir', 0777, true);

    //    touch($td.'/index.html');
    //    touch($td.'/test.txt');
    //    touch($td.'/test copy 1.txt');
    //    touch($td.'/../test copy 2.txt');
    //    touch($td.'/../test copy 3.txt');
    //    mkdir($td.'/test copy 4.txt');

    //    touch($td.'/subdir/index.html');
    //    touch($td.'/subdir/file_in_subdir.html');

    //    $driver = new LocalDriver($this->getTestDrive());
    //    $driver->pathInfoAsObject(true);
    //    $res = $driver->copyDirectory('testmount', 'testmount_copy');

    //    //var_dump($res);

    //    $fs = new Filesystem($driver);

    //    var_dump('enum:', $fs->enum('testmount/test.txt'));
    //    var_dump('backup', $fs->backup('testmount/test.txt'));
    //    var_dump('backup', $fs->backup('testmount/subdir'));

    //    var_dump($driver->listDirectory('', true));
    //    var_dump($driver->getMimeType('testmount/test.txt'));

    //    //var_dump($info->getMimetype());
    //    //var_dump($info['visibility']);
    //    //var_dump($info['mimetype']);
    //    //var_dump($info->getPermission());
    //    //var_dump($info);
    //    //

    //    var_dump($driver->writeFile('testmount/newfile.txt', 'some content'));
    //    //var_dump($info = $driver->getPathInfo('testmount/newfile.txt'));
    //    //var_dump($info = $driver->getPathInfo('testmount'));
    //}

    protected function getTestDrive()
    {
        if (null === $this->testDrive) {
            $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'fs_'.(time().rand(0, 1000));
            mkdir($dir, 0777, true);

            $this->testDrive = realpath($dir);
        }

        return $this->testDrive;
    }

    protected function needsFiles(array $files)
    {
        foreach ($files as $path => $opt) {
            $file = $this->createFile($path, $opt);
        }
    }

    protected function createFile($path, $opts)
    {
        $c = ['contents' => '', 'perm' => 0664];

        if (!is_array($opts)) {
            $c['contents'] = (string)$opts;
            $opts = $c;
        }

        $opts = array_merge($c, $opts);

        $file = $file = $this->getMount() . '/' . trim($path, '/');

        if (!is_dir($dir = dirname($file))) {
            mkdir($dir, 0775, true);
        }

        file_put_contents($file, $opts['contents']);
        chmod($file, $opts['perm']);

        return $file;
    }

    protected function needsDirs(array $dirs)
    {
        foreach ($dirs as $path => $opts) {
            if (false === $dir = $this->createDir($path, $opts)) {
                throw new \RuntimeException('Could not create test dir ' . $path);
            }
        }
    }

    protected function createDir($path, $opts)
    {

        $c = ['files' => [], 'dirs' => [], 'pem' => 0775];

        if (!is_array($opts)) {
            $opts = $c;
        }

        $opts = array_merge($c, $opts);

        $dir = $this->getMount() . '/' . $path;

        if (false === @mkdir($dir, $opts['pem'], true)) {
            return false;
        }

        foreach ($opts['files'] as $fname => $fopts) {
            $this->createFile($path . '/' . $fname, $fopts);
        }

        foreach ($opts['dirs'] as $dname => $dopts) {
            $this->createDir($path . '/' . $dname, $dopts);
        }

        return $dir;
    }

    protected function tearDown()
    {
        parent::tearDown();

        if (is_dir($this->testDrive)) {
            $this->tearDownTestDrive($this->testDrive);
        }
    }

    protected function tearDownTestDrive($file)
    {
        foreach (new \DirectoryIterator($file) as $f) {
            if ($f->isDot()) {
                continue;
            }
            if ($f->isFile() || $f->isLink()) {
                unlink($f->getRealPath());
            } elseif ($f->isDir()) {
                $this->teardownTestDrive($f->getRealPath());
            }
        }
        rmdir($file);
    }
}
