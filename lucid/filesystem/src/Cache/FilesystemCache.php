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

/**
 * @class FilesystemCache
 *
 * @package Lucid\Filesystem\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesystemCache extends AbstractCache
{
    /**
     * __construct
     *
     * @param mixed $path
     * @param mixed $ttl
     * @param string $key
     *
     * @return void
     */
    public function __construct($path, $ttl = null, $key = 'fs_cache.txt')
    {
        //$this->key = time().$key;
        $this->key = $key;
        $this->path = $path;

        parent::__construct($ttl);
    }

    public function save()
    {
        $file = $this->path.DIRECTORY_SEPARATOR.$this->key;

        if (!is_file($file)) {
            touch($file);
        }

        $mtime = filemtime($file);

        if (@file_put_contents($file, serialize($this->cache))) {
            //touch($file, $mtime);
            return true;
        }

        throw new \RuntimeException('Can\'t save file.');

        return false;
    }

    protected function load()
    {
        if (is_file($file = $this->path.DIRECTORY_SEPARATOR.$this->key)) {
            if ($this->isValid($file) && $contents = file_get_contents($file)) {
                $this->readFromStorage($contents);

                return true;
            }
        }

        return false;
    }

    protected function isValid($file)
    {
        return time() - filemtime($file) < 10;
    }
}
