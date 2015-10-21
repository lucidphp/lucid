<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Driver;

/**
 * @class ApcDriver
 * @see AbstractDriver
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ApcDriver extends AbstractDriver
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        if (!extension_loaded('apc')) {
            throw new \RuntimeException('Apc extension not loaded.');
        }
    }

    /**
     * Check if cached item exists
     *
     * @param Mixed $key
     * @return void
     */
    public function exists($key)
    {
        return apc_exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        $res = apc_fetch($key, $success);

        return $success ? $res : null;
    }

    /**
     * write data to cache
     *
     * @param String $key the cache item identifier
     * @param Mixed $data Data to be cached
     * @param Mixed $expires Integer value of the expiry time in minutes or
     * @param boolean $compressed compress data
     * unix timestamp
     * @return void
     */
    public function write($key, $data, $expires = 60, $compressed = false)
    {
        return apc_store($key, $data, $expires);
    }

    /**
     * delete a cached item
     *
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        return apc_delete($key);
    }

    /**
     * delete all cached items
     *
     * @return boolean
     */
    public function flush()
    {
        return apc_clear_cache('user');
    }

    /**
     * incrementValue
     *
     * @param string $key
     * @param int    $value
     *
     * @return int
     */
    protected function incrementValue($key, $value)
    {
        $val = apc_inc($key, $value, $success);

        return $success ? (int)$val : false;
    }

    /**
     * decrementValue
     *
     * @param string $key
     * @param int    $value
     *
     * @return int
     */
    protected function decrementValue($key, $value)
    {
        $val = apc_dec($key, $value, $success);

        return $success ? (int)$val : false;
    }
}
