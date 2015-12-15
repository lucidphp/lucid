<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Client;

use RuntimeException;

/**
 * @class XcacheClient
 * @see AbstractClient
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Xcache extends AbstractClient
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        if (!extension_loaded('xcache')) {
            throw new RuntimeException('XCache extension not loaded.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return xcache_isset($key);
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $data, $expiry = 0, $compress = false)
    {
        return xcache_set($key, serialize($data), (int)$expiry);
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        return xcache_isset($key) ? unserialize(xcache_get($key)) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return xcache_unset($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        try {
            xcache_clear_cache();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return int `$expires` as seconds.
     */
    public function parseExpireTime($expires)
    {
        return $this->expiryToSeconds($expires);
    }

    /**
     * {@inheritdoc}
     */
    protected function incrementValue($key, $value)
    {
        if (is_int($inc = xcache_inc($key, (int)$value))) {
            return $inc;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function decrementValue($key, $value)
    {
        if (is_int($dec = xcache_dec($key, (int)$value))) {
            return $dec;
        }

        return false;
    }
}
