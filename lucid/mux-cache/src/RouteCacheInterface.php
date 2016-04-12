<?php

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
interface RouteCacheInterface
{
    /**
     * Reads a routecollection from cache.
     *
     * @return RouteCacheInterface
     */
    public function read();

    /**
     * Writes a routecollection to cache.
     *
     * @param RouteCollectionInterface $routes
     *
     * @return void
     */
    public function write(RouteCollectionInterface $routes);

    /**
     * Determine if the cache is still valid.
     *
     * @return bool
     */
    public function isValid();

    /**
     * Determine if the cache already exists.
     *
     * @return bool
     */
    public function exists();

    /**
     * Gets the timestamp the last time the cache was written to.
     *
     * @return int
     */
    public function getLastWriteTime();
}
