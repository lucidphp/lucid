<?php

/*
 * This File is part of the Lucid\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Client;

use Lucid\Filesystem\PathInfo;
use Lucid\Filesystem\Permission;
use Lucid\Filesystem\Helper\PathHelper;
use Lucid\Filesystem\FilesystemInterface;

/**
 * @class AbstractDriver
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractClient implements ClientInterface
{
    /** @var PathInfo */
    protected $rootObj;

    /** @var string */
    protected $prefix;

    /** @var array */
    protected $options;

    /** @var string */
    protected $directorySeparator = '/';

    /**
     * Constructor.
     *
     * @param string $mount
     */
    public function __construct($mount)
    {
        $this->setPrefix($mount);
    }

    public function getMoundId()
    {
        return 'fs_'.hash('sha256', get_class($this).($this->prefix ?: 'prefix_none'));
    }

    /**
     * {@inheritdoc}
     */
    public function newPathInfo(
        $path,
        $type = 'file',
        $timestamp = null,
        $size = 0,
        $permission = null,
        $mimetype = null
    ) {
        $info = compact('path', 'type', 'size', 'permission', 'mimetype');

        if (PathInfo::T_DIRECTORY === $type) {
            unset($info['size']);
            unset($info['mimetype']);
            $permission = $permission ?: $this->directoryPermission();
        } else {
            $permission = $permission ?: $this->filePermission();
        }

        $info['permission'] = $permission;
        $info['timestamp'] = $timestamp ?: time();

        return $this->createPathInfo($info);
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
        $this->rootObj = $this->getPathinfo('/');
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
        if (null !== $this->rootObj) {
            return $this->rootObj->copyAttrs($info);
        }

        $pi = PathInfo::create($info);
        $pi->setDriver($this);

        return $pi;
    }

    /**
     * contentSize
     *
     * @param string $content
     *
     * @return int
     */
    protected function contentSize($content)
    {
        return PathHelper::contentSize($content);
    }

    protected function baseName($path)
    {
        return basename($path);
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
        if (0 === mb_strpos($path, '.')) {
            $path = mb_substr($path, 1);
        }

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
    protected function getUnprefixed($path, $prefix = null)
    {
        $sp = $this->directorySeparator;

        $prefix = $prefix ? trim($prefix, $sp).$sp : $this->prefix;

        if (null === $prefix || 0 !== mb_strpos(rtrim($path, $sp), rtrim($prefix, $sp), 0)) {
            return $path;
        }

        return ltrim(mb_substr($path, mb_strlen($prefix) - 1), $sp);
    }

    /**
     * setOption
     *
     * @param mixed $option
     * @param mixed $value
     *
     * @return void
     */
    protected function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * getOption
     *
     * @param mixed $option
     * @param mixed $default
     *
     * @return void
     */
    protected function getOption($option, $default = null)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        return $default;
    }

    protected function pathInfoReturnsArray()
    {
        return !$this->getOption('pathinfo_as_obj');
    }

    /**
     * filePermission
     *
     * @return int
     */
    protected function filePermission()
    {
        return $this->getOption('file_permission', 0644);
    }

    /**
     * directoryPermission
     *
     * @return int
     */
    protected function directoryPermission()
    {
        return $this->getOption('directory_permission', 0755);
    }

    protected function setInfoPermission(array &$info, $perm = null, $visibility = null)
    {
        if (null === $perm) {
            $info['permission'] = null;
            $info['visibility'] = $this instanceof SupportsVisibility ? Permission::V_PUBLIC : $visibility;
        } else {
            $info['permission'] = $this->getOption('permission_as_string') ?
                Permission::filePermsAsString($perm) :
                $perm;
            $info['visibility'] = Permission::getVisibilityFromMode($perm);
        }
    }

    /**
     * defaultOptions
     *
     * @return void
     */
    protected static function defaultOptions()
    {
        return [
            'directory_permission' => 0755,
            'file_permission' => 0664,
            'pathinfo_as_obj' => false,
            'permission_as_string' => false,
            'force_detect_mime' => false
        ];
    }
}
