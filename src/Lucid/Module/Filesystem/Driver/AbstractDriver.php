<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Driver;

use Lucid\Module\Filesystem\PathInfo;
use Lucid\Module\Filesystem\FilesystemInterface;

/**
 * @class AbstractDriver
 *
 * @package Lucid\Module\Filesystem\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractDriver implements DriverInterface
{
    protected $prefix;

    protected $lastStat;

    protected $directorySeparator = '/';

    public function getSeparator()
    {
        return $this->directorySeparator;
    }

    protected function sumPemMod($mod)
    {
        $modstr = substr(is_int($mod) ? decoct($mod) : (string)$mod, -3);

        return array_sum(str_split($modstr));
    }

    protected function getVisibilityFromMod($mod)
    {
        return 0 === ($this->sumPemMod($mod) & 0442) ?
            FilesystemInterface::PERM_PUBLIC :
            FilesystemInterface::PERM_PRIVATE;
    }

    /**
     * supportsPermMod
     *
     * @return Boolean
     */
    protected function supportsPemMod()
    {
        return false;
    }

    /**
     * createPathInfo
     *
     * @param array $info
     *
     * @return PathInfo
     */
    protected function createPathInfo(array $info)
    {
        if (null === $this->lastStat) {
            $this->lastStat = PathInfo::create($info);
        } else {
            $this->lastStat = $this->lastStat->copyAttrs($info);
        }

        return $this->lastStat;
    }

    /**
     * setPrefix
     *
     * @param mixed $path
     *
     * @return void
     */
    protected function setPrefix($path)
    {
        $prefix = 0 !== strlen($path) ?
            rtrim($path, $this->directorySeparator) . $this->directorySeparator : null;

        $this->prefix = $prefix;
    }

    /**
     * getPrefixed
     *
     * @param string $path
     *
     * @return string
     */
    protected function getPrefixed($path)
    {
        $path = ltrim($path, $this->directorySeparator);

        return 0 !== strlen($path) ? ($this->prefix ?: '') . $path : ($this->prefix ?: '');
    }
}
