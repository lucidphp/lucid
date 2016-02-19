<?php

/*
 * This File is part of the Lucid\Filesystem\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Cache;

use Lucid\Filesystem\PathInfo;
use Lucid\Filesystem\Driver\DriverInterface;

/**
 * @class AbstractCache
 *
 * @package Lucid\Filesystem\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractCache implements CacheInterface
{
    const C_RECURSIVE = 1;
    const C_COMPLETE = 2;

    protected $cache = [];
    protected $driver;
    protected $ttl;
    protected $key;

    /**
     * {@inheritdoc}
     */
    public function __construct($ttl = null, $key = 'fs_cache')
    {
        $this->key = $key;
        $this->ttl = $ttl ?: time();
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->save();
    }

    /**
     * {@inheritdoc}
     */
    public function init(DriverInterface $driver)
    {
        $this->driver = $driver;
        $this->key = $this->driver->getId().$this->key;

        $this->load();
    }

    /**
     * {@inheritdoc}
     */
    public function exists($path, $put = false, $type = null)
    {
        if ($put) {
            if (!isset($this->cache[$path])) {
                $this->cache[$path] = [false];
            } elseif (null !== $type) {
                $this->cache[$path][1] = $type;
            }
        }

        return isset($this->cache[$path]);
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        return isset($this->cache[$path][1]) && PathInfo::T_DIRECTORY === $this->cache[$path][1];
    }

    public function isType($path, $type)
    {
        return isset($this->cache[$path][1]) && $this->cache[$path][1] === $type;
    }

    public function hasType($path)
    {
        return isset($this->cache[$path][1]);
    }

    public function getCache()
    {
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function set($path, PathInfo $info, $complete = false)
    {
        $this->cache[$path] = [$complete, $info->getType(), $info];
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        // find all caches
        if ($list = $this->readList($path)) {
            foreach ($list as $pathName => $info) {
                unset($this->cache[$pathName]);
            }
        }

        unset($this->cache[$path]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($path)
    {
        if (!$this->has($path)) {
            return;
        }

        $item = $this->cache[$path][1];
        $item->setDriver($this->driver);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function read($path, $offset = null, $maxlen = null)
    {
        if (isset($this->cache[$path][2][$offset][$maxlen])) {
            return $this->cache[$path][2][$offset][$maxlen];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return isset($this->cache[$path][1]);
    }

    /**
     * {@inheritdoc}
     */
    public function addDirectories($path, $mode = null, $time = null)
    {
        $sp = $this->driver->getSeparator();
        $parts = explode($sp, $path);
        $current = '';

        do {
            $current = ltrim($current.$sp.array_shift($parts), $sp);
            $this->set($current, $this->driver->newPathInfo($current, PathInfo::T_DIRECTORY, $time ?: time(), 0, $mode));
        } while (0 < count($parts));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasContent($path, $offset = null, $maxlen = null)
    {
        return isset($this->cache[$path][2][$this->offset($offset)][$this->maxlen($maxlen)]);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($path, $contents = null, $offset = null, $maxlen = null)
    {
        $this->cache[$path][2][$this->offset($offset)][$this->maxlen($maxlen)] = $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($path, $offset = null, $maxlen = null)
    {
        if ($this->hasContent($path, $offset, $maxlen)) {
            return $this->cache[$path][2][$this->offset($offset)][$this->maxlen($maxlen)];
        }

        if (isset($this->cache[$path][2][0][-1])) {
            $max = -1 === ($pos = $this->maxlen($maxlen)) ? null : $pos;

            return mb_substr($this->cache[$path][2][0][-1], $this->offset($offset), $max, '8bit');
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function movePath($path, $newPath)
    {
        if (!isset($this->cache[$path])) {
            return false;
        }

        $this->cache[$newPath] = $this->cache[$path];

        if ($this->has($newPath)) {
            $this->get($newPath)->update(['path' => $newPath]);
        }

        $this->delete($path);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function move($dir, $newPath)
    {
        foreach ($this->cache as $path => $object) {
            $base = trim(dirname($path), '.');

            if ($path !== $base) {
                continue;
            }

            $this->movePath($path, $newPath);

            if ($this->has($path) && $this->get($path)->isDir()) {
                $sp = $this->driver->getSeparator();
                $this->moveDirectory($path, $newPath. $sp . basename($path));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, PathInfo $info, $complete = null)
    {
        if ($this->has($path)) {
            $this->cache[$path][0] = $complete !== null ? (bool)$complete : $this->cache[$path][0];
            $this->cache[$path][1] = $info;
        } else {
            $this->set($path, $info, $complete);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setComplete($path, $complete = false)
    {
        $this->cache[$path][0] = true === $complete ? self::C_RECURSIVE : $complete;
    }

    /**
     * {@inheritdoc}
     */
    public function isComplete($path, $list = false)
    {
        if (!isset($this->cache[$path][0])) {
            return false;
        }

        if (false !== $list) {
            return self::C_RECURSIVE === $this->cache[$path][0];
        }

        return in_array($this->cache[$path][0], [self::C_COMPLETE, self::C_RECURSIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function storeList($dir, array $list, $recursive = false)
    {
        $out = [];
        $complete = $recursive ? self::C_RECURSIVE : self::C_COMPLETE;

        foreach ($list as $relPath => $info) {
            $path = $info->getPath();

            if ($this->has($path)) {
                $this->cache[$path][0] = $complete;
            } else {
                $this->set($path, $info, $complete);
            }

            $out[$path] = $info;
        }

        $this->setComplete($dir, $complete);

        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function readList($dir = '', $recursive = false, array &$list = [])
    {
        if (!$this->isComplete($dir, $recursive)) {
            return false;
        }

        foreach ($this->cache as $path => $object) {
            if (!$this->has($path)) {
                return false;
            }

            if ($dir !== ($dirName = trim(dirname($path), '.'))) {
                continue;
            }

            $info = $object[1];
            $list[$path] = $info;

            if ($recursive && $info->isDir()) {
                if (false === $this->readList($path, true, $list)) {
                    return false;
                }
            }
        }

        return $list;
    }

    /**
     * inPath
     *
     * @param mixed $dir
     * @param mixed $target
     *
     * @return void
     */
    protected function inPath($dir, $target)
    {
        if ('' === $dir) {
            return true;
        }

        if (0 !== $pos = mb_strpos($target, $dir, 0, '8bit')) {
            return false;
        }

        $len = mb_strlen($dir, '8bit');

        if ('' === ($sp = mb_substr($target, $len, 1, '8bit')) || $this->driver->getSeparator() === $sp) {
            return true;
        }

        return false;
    }

    /**
     * maxlen
     *
     * @param mixed $maxlen
     *
     * @return void
     */
    protected function maxlen($maxlen)
    {
        return null === $maxlen ? -1 : $maxlen;
    }

    /**
     * offset
     *
     * @param mixed $offset
     *
     * @return void
     */
    protected function offset($offset)
    {
        return null === $offset ? 0 : $offset;
    }

    /**
     * readFromStorage
     *
     * @param mixed $data
     *
     * @return void
     */
    public function readFromStorage($data)
    {
        $cache = unserialize($data);

        if (is_array($cache)) {
            $this->cache = $cache;
        } else {
            $this->cache = [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparator()
    {
        return $this->getDriver()->getSeparator();
    }

    abstract protected function load();
}
