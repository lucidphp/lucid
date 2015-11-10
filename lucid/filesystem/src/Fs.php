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

use Lucid\Module\Filesystem\Cache\NoopCache;
use Lucid\Module\Filesystem\Cache\ArrayCache;
use Lucid\Module\Filesystem\Cache\CacheInterface;
use Lucid\Module\Filesystem\Driver\NativeDriver;
use Lucid\Module\Filesystem\Driver\DriverInterface;

/**
 * @class Fs
 *
 * @package Lucid\Module\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Fs
{
    private $cache;
    private $driver;

    /**
     * Constructor.
     *
     * @param DriverInterface $dirver
     * @param CacheInterface $cache
     */
    public function __construct(DriverInterface $driver = null, CacheInterface $cache = null)
    {
        $this->driver = $driver ?: new NativeDriver;
        $this->cache = $cache ?: new ArrayCache;
        $this->cache->init($this->driver);
    }

    /**
     * getDriver
     *
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * getCache
     *
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        if ($this->cache->exists($path)) {
            return true;
        }

        if ($this->driver->exists($path)) {
            $this->cache->exists($path, true);

            return true;
        }

        $this->cache->delete($path);

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        return $this->typeExists($path, 'isDir', PathInfo::T_DIRECTORY);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        return $this->typeExists($path, 'isFile', PathInfo::T_REGULAR);
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($path)
    {
        return $this->typeExists($path, 'isLink', PathInfo::T_SYMLINK);
    }

    /**
     * typeExists
     *
     * @param string $path
     * @param string $method
     * @param string $type
     *
     * @return boolean
     */
    protected function typeExists($path, $method, $type)
    {
        //if ($this->cache->hasType($path)) {
            //var_dump($type . ' from cache');
            //return $this->cache->isType($path, $type);
        //}

        //if (call_user_func([$this->driver, $method], $path)) {
            //$this->cache->exists($path, true, $type);

            //return true;
        //}

        return false;
    }
}
