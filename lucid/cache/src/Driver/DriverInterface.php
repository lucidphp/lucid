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
 * @interface DriverInterface
 *
 * @package Lucid\Cache\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface DriverInterface
{
    /**
     * Flag a file as compressed
     *
     * @var int
     */
    const C_COMPRESSED = 1;

    /**
     * Flag a file as uncompressed
     *
     * @var int
     */
    const C_UNCOMPRESSED = 0;

    /**
     * Check if item exists and is valid.
     *
     * @param string $key
     *
     * @return boolean `TRUE` if exists, otherwise `FALSE`
     */
    public function exists($key);

    /**
     * Get a cached item by key.
     *
     * @param string $key
     *
     * @return mixed The cached content, `null` if item doesn't exist.
     */
    public function read($key);

    /**
     * Put data to the cache.
     *
     * @param String $key the cache item identifier
     * @param mixed $data Data to be cached
     * @param int|string $expires Integer value of the expiry time in minutes or
     * a unix date format.
     * @param boolean $compressed  compress data
     * @abstract
     * @access public
     *
     * @return boolean `TRUE` on success, otherwise `FALSE`
     */
    public function write($key, $data, $expires = 60, $compressed = false);

    /**
     * Deletes an item from the cache.
     *
     * @param string $key the store key
     *
     * @return boolean `TRUE` on success, otherwise `FALSE`
     */
    public function delete($key);

    /**
     * flushCache
     *
     * @return boolean `TRUE` on success, otherwise `FALSE`
     */
    public function flush();

    /**
     * Stores the data with a far future expiry date.
     *
     * @param String $key the cache item identifier
     * @param Mixed $data Data to be cached
     * @param boolean $compressed compress data
     *
     * @return boolean `TRUE` on success, otherwise `FALSE`
     */
    public function saveForever($key, $data, $compressed = false);

    /**
     * increment
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return int the incremented value, `FALSE` on failure
     */
    public function increment($key, $value);

    /**
     * decrement
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return int the decremented value, `FALSE` on failure
     */
    public function decrement($key, $value);

    /**
     * Get default expiry time
     *
     * @return int time in minutes.
     */
    public function getDefaultExpiry();

    public function parseExpireTime($expires);
}
