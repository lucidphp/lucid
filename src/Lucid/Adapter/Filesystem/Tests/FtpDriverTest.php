<?php

/*
 * This File is part of the Lucid\Adapter\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Adapter\Filesystem\Tests;

use Lucid\Adapter\Filesystem\FtpDriver;
use Lucid\Filesystem\Driver\DriverInterface;
use Lucid\Filesystem\Tests\Driver\DriverTest;
use Lucid\Adapter\Filesystem\Tests\Helper\FtpHelper;

/**
 * @class FtpDriverTest
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FtpDriverTest extends DriverTest
{
    protected $files;

    /** @test */
    public function itShouldGetConnected()
    {
        $driver = $this->newDriver(null, [
            'host' => 'fpt.server.com',
            'user' => 'thomas',
            'password' => 'password'
        ]);

        $this->assertTrue((bool)$driver->getConnection());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function itShouldFailConnected()
    {
        $driver = $this->newDriver(null, [
            'host' => 'host.fail',
            'user' => 'thomas',
            'password' => 'password'
        ]);

        $driver->getConnection();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function itShouldFailLogin()
    {
        $driver = $this->newDriver(null, [
            'host' => 'fpt.server.com',
            'user' => 'user.fail',
            'password' => 'pwd.fail'
        ]);

        $driver->getConnection();
    }

    /**
     * {@inheritdoc}
     */
    protected function newDriver($mount = null, array $options = [])
    {
        return new FtpDriver($mount ?: $this->getMount(), $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getMount()
    {
        return '';
    }

    protected function needsFiles(array $files, $prefix = null)
    {
        foreach ($files as $path => $opt) {
            $attributes = $this->getFileOpts($opt) + ['type' => 'file'];
            $prefix = $this->getMount() . '/' . ($prefix ?: '');
            $file = rtrim($prefix, '/') . '/' . trim($path, '/');

            $this->files[trim($file, '/')] = compact('path', 'attributes');
        }
    }

    protected function needsDirs(array $dirs, $prefix = null)
    {
        foreach ($dirs as $path => $opts) {
            $opts = $this->getDirOpts($opts);

            $attributes = ['perm' => $opts['pem'], 'type' => 'dir'];

            $prefix = $this->getMount() . '/' . ($prefix ?: '');
            $dir = rtrim($prefix, '/') . '/' . trim($path, '/');
            $this->files[trim($dir, '/')] = compact('path', 'attributes');

            $this->needsFiles($opts['files'], $path);
            $this->needsDirs($opts['dirs'], $path);
        }
    }

    protected function createFile($path, $opts)
    {
        $opts = $this->getFileOpts($opts);

        $file = $file = $this->getMount() . '/' . trim($path, '/');

        if ('.' !== $dn = dirname($path)) {
            $this->needsDirs([dirname($path) => ['files' => [$path => $opts]]]);
        } else {
            $this->files['.']['contents']['files'][$path] = compact('path') + $opts;
        }

        return $file;
    }

    protected function makeFile($path, $opts)
    {
        $opts = $this->getFileOpts($opts);

        return compact('path') + $opts;
    }

    protected function getFileOpts($opts)
    {
        $c = ['contents' => '', 'perm' => 0664, 'type' => 'file'];

        if (!is_array($opts)) {
            $c['contents'] = (string)$opts;
            $opts = $c;
        }

        return array_merge($c, $opts);
    }

    protected function getDirOpts($opts)
    {
        $c = ['files' => [], 'dirs' => [], 'pem' => 0775, 'type' => 'dir'];

        if (!is_array($opts)) {
            $opts = $c;
        }

        return array_merge($c, $opts);
    }

    protected function createDir($path, $opts)
    {
        $c = ['files' => [], 'dirs' => [], 'pem' => 0775];

        if (!is_array($opts)) {
            $opts = $c;
        }

        $opts = array_merge($c, $opts);

        $dir = $this->getMount() . '/' . $path;


        $parts = explode('/', $dir);
        $contents = &$this->files['.']['contents'];

        while (0 < count($parts)) {

            if (!$n = array_shift($parts)) {
                continue;
            }

            if (!isset($contents['dirs'][$n])) {
                $contents['dirs'][$n] = [
                    'path' => $n,
                    'contents' => [],
                    'permission' => [$opts['pem']]
                ];
            }

            $contents = &$contents['dirs'][$n]['contents'];
        }

        foreach ($opts['files'] as $fname => $fopts) {
            if (isset($contents['files'][$fname])) {
                continue;
            }
            $contents['files'][$fname] = $this->makeFile($fname, $fopts);
        }

        foreach ($opts['dirs'] as $dname => $dopts) {
            $this->createDir($path . '/' . $dname, $dopts);
        }

        return $dir;
    }

    protected function setUp()
    {
        $this->files = [
            '.' => ['pem' => 0775]
        ];

        $files = &$this->files;
        FtpHelper::$files =&$this->files;
        require_once __DIR__.'/Fixures/ftphelper.php';
    }

    protected function tearDown()
    {
        FtpHelper::$files = null;
        parent::tearDown();
    }

    protected function createDirList(array $files)
    {
        foreach ($files as $path => $opt) {
            $ln = '';
        }
    }
}
