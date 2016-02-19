<?php

/*
 * This File is part of the Lucid\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem;

use Lucid\Filesystem\Cache\ArrayCache;
use Lucid\Filesystem\Driver\LocalDriver;
use Lucid\Filesystem\Driver\DriverInterface;
use Lucid\Filesystem\Cache\CacheInterface;
use Lucid\Filesystem\Helper\PathHelper;
use Lucid\Filesystem\Exception\IOException;

/**
 * @class Filesystem
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Filesystem implements FilesystemInterface
{
    private $cache;
    private $driver;

    /**
     * Constructor.
     *
     * @param DriverInterface $driver
     * @param CacheInterface $cache
     */
    public function __construct(DriverInterface $driver = null, CacheInterface $cache = null)
    {
        $this->driver = $driver ?: new LocalDriver('/');
        $this->cache = $cache ?: new ArrayCache;
        $this->cache->setDriver($this->driver);
        $this->cache->init();
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo($path)
    {
        if ($this->exists($path)) {
            return $this->cache->get($path);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        if (false !== $this->cache->has($path)) {
            return true;
        }

        if ($this->driver->exists($path)) {
            $this->cache->set($path, $this->driver->getPathInfo($path));

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        if ($this->exists($path)) {
            return $this->cache->get($path)->isDir();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        if ($this->exists($path)) {
            return $this->cache->get($path)->isFile();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($path)
    {
        if ($this->exists($path)) {
            return $this->cache->get($path)->isLink();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectory($path)
    {
        if ($this->exists($path) && $this->cache->get($path)->isDir()) {
            return true;
        }

        if ($this->driver->ensureDirectory($path)) {
            $this->cache->set($path, $this->driver->getPathInfo($path));

            return true;
        }

        throw IOException::createDir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($path)
    {
        if ($this->exists($path) && $this->cache->get($path)->isFile()) {
            return true;
        }

        if ($this->driver->ensureFile($path)) {
            $this->cache->set($path, $this->driver->getPathInfo($path));

            return true;
        }

        throw IOException::createFile($path);
    }

    /**
     * {@inheritdoc}
     */
    public function writeFile($file, $content)
    {
        if (false === $bytes = $this->driver->writeFile($file, $content)) {
            throw IOException::writeFile($file);
        }

        $this->cache->setContent($file, $content, null, null);

        return $bytes;
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($file, $contents)
    {
        if (false === $bytes = $this->driver->updateFile($file, $content)) {
            throw IOException::writeFile($file);
        }

        $this->cache->setContent($file, $content, null, null);

        return $bytes;
    }

    /**
     * {@inheritdoc}
     */
    public function putFile($file, $contents)
    {
        if ($this->isFile($file)) {
            return $this->updateFile($file, $contents);
        }

        return $this->writeFile($file, $contents);
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($file, $offset = null, $maxlen = null)
    {
        if (false !== $content = $this->cache->getContent($file, $offset, $maxlen)) {
            return $content;
        }

        if (false !== $content = $this->driver->readFile($file, $offset, $maxlen)) {
            $this->cache->setContent($file, $content, $offset, $maxlen);

            return $content;
        }

        throw IOException::readFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($file, $stream, $offset = null, $maxlen = null)
    {
        $pos = ftell($stream);

        if (false === $bytes = $this->driver->writeStream($file, $stream, $offset, $maxlen)) {
            throw IOException::writeFile($file);
        }

        $this->cache->setContent(
            $file,
            stream_get_contents($stream, $pos + (null !== $offset ? $offset : 0), $bytes),
            null,
            null
        );

        return $bytes;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($file, $stream, $offset = null, $maxlen = null)
    {
        $pos = ftell($stream);

        if (false === $bytes = $this->driver->writeStream($file, $stream, $offset, $maxlen)) {
            throw IOException::writeFile($file);
        }

        $this->cache->setContent(
            $file,
            stream_get_contents($stream, $pos + (null !== $offset ? $offset : 0), $bytes),
            null,
            null
        );

        return $bytes;
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($file)
    {
        if (false !== $content = $this->cache->getContent($file, null, null)) {
            $tmp = tmpfile();

            fwrite($tmp, $content);
            rewind($tmp);

            return $tmp;
        }

        if (false !== $stream = $this->driver->readStream($file)) {
            $this->cache->setContent($file, stream_get_contents($stream), null, null);
            rewind($stream);

            return $stream;
        }

        throw IOException::readFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir($dir, $mode = null, $recursive = true)
    {
        if (false === $this->driver->createDirectory($dir, $mode, $recursive)) {
            throw IOException::createDir($dir);
        }

        return $this->cache->addDirectories($dir, $mode, time());
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir($dir)
    {
        if ($this->driver->deleteDirectory($dir)) {
            $this->cache->delete($dir);

            return true;
        }

        throw IOException::rmDir($dir);
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($file)
    {
        if ($this->driver->deleteFile($file)) {
            $this->cache->delete($file);

            return true;
        }

        throw IOException::rmFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($path)
    {
        if ($this->isDir()) {
            return $this->rmdir($path);
        }

        if ($this->isFile()) {
            return $this->unlink($path);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($source, $target)
    {
        if ($this->driver->rename($source, $target)) {
            $this->cache->move($source, $target);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function listDirectory($path, $recursive = false)
    {
        if (false !== $list = $this->cache->readList($path, $recursive)) {
            return $list;
        }

        if (false !== $list = $this->driver->listDirectory($path, $recursive)) {
            return $this->cache->storeList($path, $list, $recursive);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function enum($path, $prefix = null, $pad = true)
    {
        if (!$list = $this->listDirectory($dir = PathHelper::dirName($path))) {
            var_dump('no list');
            var_dump($dir);
            return $path;
        }

        $start = 0;
        $files = array_change_key_case($list, CASE_LOWER);


        $prefix = $prefix ?: self::COPY_PREFIX;

        $name = $bn = PathHelper::baseName($path);
        $suffix = '';

        if (false !== $pos = strrpos($bn, '.')) {
            $suffix = substr($bn, $pos);
            $bn     = substr($bn, 0, $pos);
        }

        $sp = $this->driver->getSeparator();
        $enum = '';

        while (isset($files[trim(strtolower($dir . $sp . $name), $sp)])) {
            $name = $bn . ' ' . $prefix . $enum . $suffix;
            $enum = 0 < $start ? ' '.$start : '';
            $start++;
        }

        return trim($dir . $sp . $name, $sp);
    }

    /**
     * {@inheritdoc}
     */
    public function touch($path, $time = null, $atime = null)
    {
        if ($this->driver instanceof TouchableInterface) {
            $this->driver->touch($path, $time, $atime);

            return true;
        }

        if ($this->writeFile($path, null)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($path, $mode = 0755, $recursive = true, $umask = 0000)
    {
        if (!$this->driver->setPermission($path, $mode, $recursive)) {
            return false;
        }
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

        $pname = dirname($path) . $this->getSeparator().$suffix.basename($path);
        $backupName = $this->enum($pname, (new \DateTime())->format($dateFormat));

        if ($this->isFile($path)) {
            return $this->copyFile($path, $backupName);
        }

        return $this->copyDirectory($path, $backupName);
    }

    /**
     * {@inheritdoc}
     */
    public function copy($source, $target = null)
    {
        if (null === $target) {
            $target = $this->enum($source);
        }

        if ($this->driver->isFile($source)) {
            return $this->driver->copyFile($target);
        }

        return $this->driver->copyDirectory($target);
    }

    public function chown($file, $owner, $recursive = true)
    {
    }

    public function chgrp($file, $group, $recursive = true)
    {
    }

    public function flush($path)
    {
        $this->driver->deleteDirectory($path);
    }

    /**
     * getSeparator
     *
     * @return string
     */
    protected function getSeparator()
    {
        if ($this->driver instanceof AbstractDriver) {
            return $this->driver->getSeparator();
        }

        return '/';
    }
}
