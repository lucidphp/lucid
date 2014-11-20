<?php

/*
 * This File is part of the Lucid\Module\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Cache\Driver;

use Memcached;

/**
 * @class MemcachedDriver
 * @see AbstractDriver
 *
 * @package Lucid\Module
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MemcachedDriver extends AbstractDriver
{
    /**
     * Memcached instance
     *
     * @var Memcached
     */
    protected $driver;

    /**
     * Constructor.
     *
     * @param Memcached $memcached
     *
     * @return void
     */
    public function __construct(Memcached $memcached)
    {
        $this->driver = $memcached;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        if (false === $this->driver->get($key)) {
            return Memcached::RES_NOTFOUND !== $this->driver->getResultCode();
        }

        return true;

    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        $res = $this->driver->get($key);

        return Memcached::RES_NOTFOUND === $this->driver->getResultCode() ? null : $res;
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $data, $expires = 60, $compressed = false)
    {
        $cmp = $this->driver->getOption(Memcached::OPT_COMPRESSION);

        $this->driver->setOption(Memcached::OPT_COMPRESSION, $compressed);

        $cached = $this->driver->set($key, $data, $expires);

        $this->driver->setOption(Memcached::OPT_COMPRESSION, $cmp);

        return $cached;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->driver->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->driver->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @return int Unix timestamp
     */
    public function parseExpireTime($expires)
    {
        return $this->expiryToUnixTimestamp($expires);
    }

    /**
     * {@inheritdoc}
     */
    protected function incrementValue($key, $value)
    {
        return $this->driver->increment($key, $value);
    }

    /**
     * incrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    protected function decrementValue($key, $value)
    {
        return $this->driver->decrement($key, $value);
    }
}
