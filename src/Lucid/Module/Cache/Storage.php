<?php

/*
 * This File is part of the Lucid\Module\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Cache;

use Closure;
use ArrayAccess;
use Lucid\Module\Cache\Driver\DriverInterface;

/**
 * @class Storage
 * @see CacheInterface
 * @see ArrayAccess
 *
 * @package Lucid\Module\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Storage implements CacheInterface, SectionableInterface, ArrayAccess
{
    /**
     * Array containing already retrieved items from the caching source.
     *
     * @var array
     */
    protected $pool = [];

    /**
     * Cache driver instance
     *
     * @var DriverInterface
     */
    protected $driver;

    /**
     * cache id prefix
     *
     * @var string
     */
    protected $prefix;

    /**
     * Constructor.
     *
     * @param DriverInterface $cachDriver
     * @param string          $prefix cache id prefix
     */
    public function __construct(DriverInterface $cachDriver, $prefix = 'cache')
    {
        $this->driver = $cachDriver;
        $this->setCachePrefix($prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if (isset($this->pool[$key = $this->getPrefixed($key)])) {
            return true;
        }

        return $this->driver->exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (isset($this->pool[$key = $this->getPrefixed($key)])) {
            return $this->pool[$key];
        }

        if ($data = $this->driver->read($key)) {
            return $data;
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $expires = 60, $compressed = false)
    {
        $expires = $this->driver->parseExpireTime($expires);

        if ($this->driver->write($key = $this->getPrefixed($key), $data, $expires, $compressed)) {
            $this->pool[$key] = $this->driver->read($key);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function persist($key, $data, $compressed = false)
    {
        return $this->driver->saveForever($this->getPrefixed($key), $data, $compressed);
    }

    /**
     * {@inheritdoc}
     */
    public function setUsing($key, callable $callback, $expires = null, $compressed = false)
    {
        $this->set($key, $default = $this->execDefaultVal($key, $callback), $expires, $compressed);

        return $default;

    }

    /**
     * {@inheritdoc}
     */
    public function persistUsing($key, callable $callback, $compressed = false)
    {
        $this->persist($key, $default = $this->execDefaultVal($key, $callback), $compressed);

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function purge($key = null)
    {
        if (null === ($key)) {
            $this->pool = [];

            return $this->driver->flush();
        }

        if ($del = $this->driver->delete($key = $this->getPrefixed($key))) {
            unset($this->pool[$key]);
        }

        return (bool)$del;
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $value = 1)
    {
        if ($this->driver->increment($key = $this->getPrefixed($key), $value)) {
            unset($this->pool[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decrement($key, $value = 1)
    {
        if ($this->driver->decrement($key = $this->getPrefixed($key), $value)) {
            unset($this->pool[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function section($section)
    {
        return new Section($this, $section);
    }

    /**
     * {@inheritdoc}
     *
     * @see CacheInterface::has()
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     *
     * @see CacheInterface::purge()
     */
    public function offsetUnset($offset)
    {
        return $this->purge($offset);
    }

    /**
     * {@inheritdoc}
     *
     * @see CacheInterface::set()
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @see CacheInterface::get()
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * getPrefixed
     *
     * @param Mixed $key
     * @return void
     */
    protected function getPrefixed($key)
    {
        return null !== $this->prefix ? sprintf("%s_%s", $this->prefix, $key) : $key;
    }

    /**
     * setCachePrefix
     *
     * @param Mixed $prefix
     * @return void
     */
    protected function setCachePrefix($prefix = null)
    {
        $this->prefix = $prefix;
    }

    /**
     * execDefaultVal
     *
     * @param Mixed   $key
     * @param Closure $callback
     * @return void
     */
    protected function execDefaultVal($key, callable $callback)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        return call_user_func($callback);
    }
}
