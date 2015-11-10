<?php

/*
 * This File is part of the Lucid\Mux package
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
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteCacheInterface
{
    /**
     * read
     *
     * @return void
     */
    public function read();

    /**
     * write
     *
     * @param RouteCollectionInterface $routes
     *
     * @return void
     */
    public function write(RouteCollectionInterface $routes);

    /**
     * isValid
     *
     * @return boolean
     */
    public function isValid();

    /**
     * exists
     *
     * @return boolean
     */
    public function exists();

    /**
     * getLastWriteTime
     *
     * @return int
     */
    public function getLastWriteTime();
}
