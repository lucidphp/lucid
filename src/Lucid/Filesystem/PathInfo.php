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

use Lucid\Filesystem\Driver\DriverInterface;

/**
 * @class PathInfo
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PathInfo implements \Serializable, \ArrayAccess
{
    const T_DIRECTORY = 'dir';
    const T_REGULAR = 'file';
    const T_SYMLINK = 'link';

    /**
     * driver
     *
     * @var \Closure
     */
    protected $driver;

    /**
     * attributes
     *
     * @var array
     */
    protected $attributes;

    /**
     * allowedAttributes
     *
     * @var array
     */
    protected static $allowedAttributes = [
        'type', 'path', 'size', 'timestamp',
        'visibility', 'permission', 'mimetype',
    ];

    /**
     * Constructor.
     *
     * @param string $type
     * @param string $path
     * @param int    $mtime
     * @param int    $size
     * @param string $visibility
     * @param string $perm
     * @param string $mime
     */
    public function __construct($type, $path, $mtime, $size = null, $visibility = null, $perm = null, $mime = null)
    {
        $this->setAttrs($type, $path, $mtime, $size, $visibility, $perm, $mime);
    }

    /**
     * isDir
     *
     * @return boolean
     */
    public function isDir()
    {
        return self::T_DIRECTORY === $this->getType();
    }

    /**
     * isFile
     *
     * @return boolean
     */
    public function isFile()
    {
        return self::T_REGULAR === $this->getType();
    }

    /**
     * isLink
     *
     * @return boolean
     */
    public function isLink()
    {
        return self::T_SYMLINK === $this->getType();
    }

    /**
     * getPath
     *
     * @return string
     */
    public function getPath()
    {
        return $this->doGet('path');
    }

    /**
     * getPath
     *
     * @return string
     */
    public function getRealPath()
    {
        return null;
    }

    /**
     * getType
     *
     * @return string
     */
    public function getType()
    {
        return $this->doGet('type');
    }

    /**
     * getSize
     *
     * @return int
     */
    public function getSize()
    {
        return $this->doGet('size');
    }

    /**
     * getMimetype
     *
     * @return void
     */
    public function getMimetype()
    {
        if ($this->isDir()) {
            return;
        }

        if (null === $this->attributes['mimetype'] && null !== $this->driver) {
            $mime = $this->driver->getMimeType($this->getPath());
            $this->attributes['mimetype'] = $mime;
        }

        return $this->doGet('mimetype');
    }

    /**
     * getPermission
     *
     * @return string|null
     */
    public function getVisibility()
    {
        if (null !== $this->attributes['visibility']) {
            return $this->attributes['visibility'];
        }

        if (null !== $this->attributes['permission']) {
            return $this->attributes['visibility'] = Permission::getVisibilityFromMode($this->attributes['permission']);
        }
    }

    /**
     * getPermission
     *
     * @return string|null
     */
    public function getPermission()
    {
        $this->ensureVisiblity();

        return new Permission($this->attributes['permission'], $this->attributes['visibility']);
    }

    /**
     * get
     *
     * @param mixed $attribute
     *
     * @return void
     */
    public function get($attribute)
    {
        if ($method = $this->methodExists($key)) {
            return $this->{$method}();
        }

        return $this->doGet($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getPath() ?: '';
    }

    /**
     * cloneArgs
     *
     * @param array $input
     *
     * @return PathInfo
     */
    public function copyAttrs(array $input)
    {
        $cl = static::create($input);

        if ($this->driver) {
            $cl->setDriver($this->driver);
        }

        return $cl;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $this->attributes = unserialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($key)
    {
        if ($method = $this->methodExists($key)) {
            return $this->{$method}();
        }

        return $this->doGet($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value)
    {
        throw new \LogicException('readonly object.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($key)
    {
        throw new \LogicException('readonly object.');
    }

    /**
     * setDriver
     *
     * @param DriverInterface $getDriver
     *
     * @return void
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * update
     *
     * @param array $info
     *
     * @return void
     */
    public function update(array $info)
    {
        $data = static::sanitizeInput($info);

        // type musn't change
        unset($data['type']);

        $this->attributes = array_merge($this->attributes, $data);
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * doGet
     *
     * @param mixed $key
     *
     * @return mixed
     */
    protected function doGet($key, $default = null)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * methodExists
     *
     * @param string $attr
     *
     * @return string|boolean
     */
    protected function methodExists($attr)
    {
        if (!method_exists($this, $method = 'get'.ucfirst($attr))) {
            return false;
        }

        return $method;
    }

    /**
     * sanitizeInput
     *
     * @param array $input
     *
     * @return array
     */
    protected static function sanitizeInput(array $input)
    {
        $attrs = array_combine(
            static::$allowedAttributes,
            array_fill(0, count(static::$allowedAttributes), null)
        );

        //$mod = array_intersect_key($attrs, $input);

        return array_merge($attrs, $input);
    }

    /**
     * Creates a new PathInfo Object.
     *
     * @param array $info
     *
     * @return void
     */
    public static function create(array $info)
    {
        $data = static::sanitizeInput($info);

        return new static(
            $data['type'],
            $data['path'],
            $data['timestamp'],
            $data['size'],
            $data['visibility'],
            $data['permission'],
            $data['mimetype']
        );
    }

    /**
     * ensureVisiblity
     *
     * @return void
     */
    protected function ensureVisiblity()
    {
        if (null === $this->attributes['visibility'] && null !== $this->driver) {
            //$pem = $this->driver->getPermission($this->getPath());
            //$this->attributes['permission'] = $pem['permission'];
            //$this->attributes['visibility'] = $pem['visibility'];
        }
    }

    /**
     * setAttrs
     *
     * @param string $type
     * @param string $path
     * @param int    $timestamp
     * @param int    $size
     * @param string $visibility
     * @param string $permission
     * @param string $mimetype
     *
     * @return void
     */
    protected function setAttrs($type, $path, $timestamp, $size, $visibility, $permission, $mimetype)
    {
        $attributes = compact('type', 'path', 'timestamp', 'size', 'visibility', 'permission', 'mimetype');

        if (self::T_DIRECTORY === $type) {
            unset($attributes['size']);
            unset($attributes['mimetype']);
        }

        $this->attributes = $attributes;
    }
}
