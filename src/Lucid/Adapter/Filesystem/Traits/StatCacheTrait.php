<?php

/*
 * This File is part of the Lucid\Adapter\Filesystem\Traits package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Filesystem\Traits;

/**
 * @class StatCacheTrait
 *
 * @package Lucid\Adapter\Filesystem\Traits
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait StatCacheTrait
{
    /**
     * statCache
     *
     * @var array
     */
    protected $statCache = [];

    /**
     * getStat
     *
     * @param string $path
     *
     * @return array
     */
    protected function getStat($path)
    {
        if (isset($this->statCache[$path])) {
            return $this->statCache[$path];
        }

        return $this->statCache[$path] = $this->statPath($path);
    }

    /**
     * clearStat
     *
     * @param mixed $path
     *
     * @return void
     */
    protected function clearStat($path)
    {
        unset($this->statCache[$path]);
    }

    /**
     * reStat
     *
     * @param mixed $path
     * @param mixed $newPath
     *
     * @return void
     */
    protected function reStat($path, $newPath)
    {
        if (isset($this->statCache[$path])) {
            $this->statCache[$newPath] = $this->statCache[$path];
            $this->clearStat($path);
        }
    }

    /**
     * hasStat
     *
     * @param mixed $path
     *
     * @return boolean
     */
    protected function hasStat($path)
    {
        return isset($this->statCache[$path]);
    }

    /**
     * statPath
     *
     * @param string $path
     *
     * @return void
     */
    abstract protected function statPath($path);
}
