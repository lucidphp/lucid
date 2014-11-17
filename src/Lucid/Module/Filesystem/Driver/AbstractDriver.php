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
    /**
     * rootObj
     *
     * @var PathInfo
     */
    protected $rootObj;

    /**
     * prefix
     *
     * @var string|null
     */
    protected $prefix;

    /**
     * directorySeparator
     *
     * @var string
     */
    protected $directorySeparator = '/';

    /**
     * Constructor.
     */
    public function __construct($mount)
    {
        $this->setPrefix($mount);
        $this->setRootObject();
    }

    /**
     * getSeparator
     *
     * @return void
     */
    public function getSeparator()
    {
        return $this->directorySeparator;
    }

    /**
     * setRootObject
     *
     * @return void
     */
    protected function setRootObject()
    {
        $robj = !$this->pathInfoReturnsArray();
        $this->pathInfoAsObject(true);
        $this->rootObj = $this->getPathinfo('/');
        $this->pathInfoAsObject($robj);
    }

    /**
     * sumPemMod
     *
     * @param mixed $mod
     *
     * @return void
     */
    protected function sumPemMod($mod)
    {
        $modstr = substr(is_int($mod) ? decoct($mod) : (string)$mod, -3);

        return array_sum(str_split($modstr));
    }

    /**
     * getVisibilityFromMod
     *
     * @param mixed $mod
     *
     * @return void
     */
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
        if ($this->pathInfoReturnsArray()) {
            return $info;
        }

        if (null !== $this->rootObj) {
            return $this->rootObj->copyAttrs($info);
        }

        $pi = PathInfo::create($info);
        $pi->setDriver($this);

        return $pi;
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

        return 0 !== mb_strlen($path) ? ($this->prefix ?: '') . $path : ($this->prefix ?: '');
    }

    /**
     * removePrefix
     *
     * @param mixed $path
     *
     * @return string
     */
    protected function getUnprefixed($path)
    {
        if (null === $this->prefix || 0 !== mb_strpos($path, $this->prefix)) {
            return $path;
        }

        return mb_substr($path, mb_strlen($this->prefix) - 1);
    }

    abstract protected function pathInfoReturnsArray();
}
