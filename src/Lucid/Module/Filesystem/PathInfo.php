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

/**
 * @class PathInfo
 *
 * @package Lucid\Module\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PathInfo implements \Serializable, \ArrayAccess
{
    protected $attributes;

    /**
     * Constructor
     *
     * @param string $type
     * @param string $path
     * @param int    $size
     * @param string $permission
     * @param int    $mod
     */
    public function __construct($type, $path, $size = null, $timestamp = null, $permission = null, $mod = null)
    {
        $this->attributes = compact('type', 'path', 'size', 'timestamp', 'permission', 'mod', 'mimetype');
    }

    /**
     * isDir
     *
     * @return boolean
     */
    public function isDir()
    {
        return 'directory' === $this->getType();
    }

    /**
     * isFile
     *
     * @return boolean
     */
    public function isFile()
    {
        return 'file' === $this->getType();
    }

    /**
     * isLink
     *
     * @return boolean
     */
    public function isLink()
    {
        return 'link' === $this->getType();
    }

    /**
     * getPath
     *
     * @return string
     */
    public function getPath()
    {
        return $this->get('path');
    }

    /**
     * getType
     *
     * @return string
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * getSize
     *
     * @return int
     */
    public function getSize()
    {
        return $this->get('size');
    }

    /**
     * getPermission
     *
     * @return string|null
     */
    public function getPermission()
    {
        return $this->get('permission');
    }

    /**
     * getPermission
     *
     * @return string|null
     */
    public function getMod()
    {
        return $this->get('mod');
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
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        }
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
        $cl = clone $this;
        $cl->attributes = static::sanitizeInput($input);

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
        return $this->attributes[$key];
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
     * Creates a new PathInfo Object.
     *
     * @param array $info
     *
     * @return void
     */
    public static function create(array $info)
    {
        $data = serialize(static::sanitizeInput($info));

        return unserialize(
            sprintf('C:%d:"%s":%d:{%s}', strlen(__CLASS__), __CLASS__, strlen($data), $data)
        );
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
        $out = [];

        foreach (['type', 'path', 'timestamp', 'size', 'permission', 'mod', 'mimetype'] as $key) {
            if (isset($input[$key])) {
                $out[$key] = $input[$key];
            } else {
                $out[$key] = null;
            }
        }

        return $out;
    }
}
