<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache;

use Lucid\Mux\RouteCollectionInterface;

/**
 * @interface RouteCacheInterface
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface StorageInterface
{
    /** @var mixed */
    const DEFAULT_PREFIX = 'lucid_mux_cache_storage';

    /**
     * Reads a RouteCollection from cache.
     *
     * @return CachedCollectionInterface
     */
    public function read() : ?CachedCollectionInterface;

    /**
     * Writes a RouteCollection to the cache.
     *
     * @param RouteCollectionInterface $routes
     *
     * @return void
     */
    public function write(RouteCollectionInterface $routes) : void;

    /**
     * Determine if the cache is still valid.
     *
     * @var int $time unix timestamp
     *
     * @return bool
     */
    public function isValid(int $time) : bool;

    /**
     * Determine if the cache already exists.
     *
     * @return bool
     */
    public function exists() : bool;

    /**
     * Gets the timestamp the last time the cache was written to.
     *
     * @return int
     */
    public function getLastWriteTime() : int;
}
