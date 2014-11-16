<?php

/*
 * This File is part of the Lucid\Module\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem;

use Lucid\Module\Filesystem\Driver\LocalDriver;
use Lucid\Module\Filesystem\Driver\DriverInterface;
use Lucid\Module\Filesystem\Exception\IOException;

/**
 * @class Filesystem
 *
 * @package Lucid\Module\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Filesystem implements FilesystemInterface
{
    private $driver;

    /**
     * Constructor.
     *
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver = null)
    {
        $this->driver = $driver ?: new LocalDriver('/');
    }

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        return $this->driver->exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        return $this->driver->isDir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        return $this->driver->isFile($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($file)
    {
        return $this->driver->isLink($path);
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectory($dir)
    {
        return $this->driver->ensureDirectory($dir);
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($file)
    {
        return $this->driver->ensureFile($dir);
    }

    /**
     * {@inheritdoc}
     */
    public function setContents($file, $content)
    {
        if (!$this->driver->isFile($file)) {
            throw new IOException(sprintf('Cannot write content. %s is not a file', $file));
        }

        return $this->driver->writeFile($file, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStreamToFile($file, $stream)
    {
        return $this->driver->writeStream($file, $stream);
    }

    /**
     * {@inheritdoc}
     */
    public function readStreamFromFile($file)
    {
        return $this->driver->readStream($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents($file, $start = null, $stop = null)
    {
        if (!$this->driver->isFile($file)) {
            throw new IOException(sprintf('Cannot read content. %s is not a file', $file));
        }

        return $this->driver->readFile($file, $start, $stop);
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir($dir, $mod = 0755, $recursive = true)
    {
        if ($res = $this->driver->createDirectory($dir, $mod, $recursive)) {
            return $res;
        }

        throw IOException::createDir($dir);
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir($dir)
    {
        if (!$this->driver->deleteDirectory($dir)) {
            throw IOException::rmDir($dir);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($file)
    {
        return $this->driver->deleteFile($file);
    }

    /**
     * remove
     *
     * @param string $path
     *
     * @return void
     */
    public function remove($path)
    {
        if ($this->driver->isDir($path)) {
            return $this->driver->deleteDirectory($path);
        }

        try {
            return $this->driver->removeFile($path);
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($source, $target, $overwrite = false)
    {
        if ($overwrite) {
        }

        return $this->driver->rename($source, $target);
    }

    /**
     * {@inheritdoc}
     */
    public function enum($path, $start = 1, $prefix = null, $pad = true)
    {
        $files = array_change_key_case($this->driver->listDirectory($dir = dirname($path)), CASE_LOWER);

        $prefix = $prefix ?: self::COPY_PREFIX;

        $name = $bn = basename($path);
        $suffix = '';

        if (false !== $pos = strrpos($bn, '.')) {
            $suffix = substr($bn, $pos);
            $bn     = substr($bn, 0, $pos);
        }

        while (isset($files[strtolower($name = $bn . ' ' . $prefix . ' ' . $start . $suffix)])) {
            $start++;
        }

        return $dir . $this->driver->getSeparator() . $name;
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($path, $mod = 0755, $recursive = true, $umask = 0000)
    {
        $stat = $this->driver->setPermission($path, $mod, $recursive);

        return $stat;
    }

    /**
     * {@inheritdoc}
     */
    public function backup($path, $dateFormat = 'Y-m-d-His', $suffix = '~')
    {
        if (!$this->driver->exists($path)) {
            throw new IOException();
        }

        if ($this->driver->isLink($path)) {
            return false;
        }

        $pname = dirname($path) . $this->driver->getSeparator().$suffix.basename($path);
        $backupName = $this->enum($pname, 1, (new \DateTime())->format($dateFormat));

        if ($this->driver->isFile($path)) {
            return $this->driver->copyFile($path, $backupName);
        }

        return $this->driver->copyDirectory($path, $backupName);
    }

    public function flush($directory)
    {
    }

    public function touch($file, $time = null, $atime = null)
    {
    }

    public function copy($source, $target = null, $replace = false)
    {
    }

    public function chown($file, $owner, $recursive = true)
    {
    }
    public function chgrp($file, $group, $recursive = true)
    {
    }

}
