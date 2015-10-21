<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Cache;

use Closure;

/**
 * @interface CacheInterface
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CacheInterface
{
    const COMPRESSED = true;

    const UNCOMPRESSED = false;

    const PERSIST = -1;

    /**
     * Check if an item is already cached.
     *
     * @param string $key the cache item identifier.
     *
     * @return boolean `TRUE` if exists, otherwise `FALSE`
     */
    public function has($key);

    /**
     * Retreive cached data by key.
     *
     * @param string $key    the storage key.
     * @param mixed $default the default value to return if nothing is found.
     *
     * @return mixed|null The cached data or `NULL` if no object was found
     */
    public function get($key, $default = null);

    /**
     * Write data to cache.
     *
     * @param string     $key the storage key.
     * @param mixed      $data the data to be stored.
     * @param int|string $expires expirey time in minutes or as valid UNIX date format.
     * If a negative integer `-1` is passed, the cache will persist and not expire.
     * @param boolean    $compressed compress data when writing. Some caching
     * engies will not offer compression. In this case, `$compress` will be
     * ignored.
     *
     * @return boolean `TRUE` on success, `FALSE` on error
     */
    public function set($key, $data, $expires = 60, $compressed = false);

    /**
     * Persists a data set.
     *
     * Caches data with a far future expiry time.
     *
     * @see CacheInterface::set()
     *
     * @return boolean `TRUE` on success, `FALSE` on error
     */
    public function persist($key, $data, $compressed = false);

    /**
     * Increments a numeric value stored in the cache.
     *
     * @param string $key    the storage key.
     * @param int    $value  the value to increment by.
     *
     * @return int the incremented value.
     */
    public function increment($key, $value = 1);

    /**
     * Decrements a numeric value stored in the cache.
     *
     * @param string $key    the storage key.
     * @param int    $value  the value to decrement by.
     *
     * @return int the decremented value.
     */
    public function decrement($key, $value = 1);

    /**
     * Deletes data from cache.
     *
     * If `$key` is ommitted the whole soreage will be wiped, otherwise, the
     * item stored under `$key` will be deleted.
     *
     * @param string $key the storage key
     * @return void
     */
    public function purge($key = null);

    /**
     * Writes default data to cache.
     *
     * @param string     $key        the storage key
     * @param callable   $callback   a callback to return the default data
     * if a dataset for `$key` does not exist
     * @param int|string $expires    expirey time in minutes or as valid UNIX date format.
     * @param boolean    $compressed compress data
     *
     * @return mixed the cached item for `$key` or the results of the `$callback`
     */
    public function setUsing($key, callable $callback, $expires = null, $compressed = false);

    /**
     * Writes default data to cache with a far future expiry date.
     *
     * @param string   $key        the cache item identifier
     * @param callable $callback   A callback to returns the default data
     * if a dataset for `$key` does not exist
     * @param boolean  $compressed compress data on storage
     *
     * @return mixed the cached item for `$key` or the results of the `$callback`
     */
    public function persistUsing($key, callable $callback, $compressed = false);
}
