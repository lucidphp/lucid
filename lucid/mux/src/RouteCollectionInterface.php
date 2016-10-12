<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

/**
 * @interface RouteCollectionInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteCollectionInterface
{
    /**
     * Get a route by name.
     *
     * @param string $routeName the name of the route.
     *
     * @return RouteInterface
     */
    public function get(string $routeName) : RouteInterface;

    /**
     * Should check if a route exists with the given name.
     *
     * @param string $routeName the name of the route.
     *
     * @return bool
     */
    public function has(string $routeName) : bool;

    /**
     * Get all registered routes as array.
     *
     * @return RouteInterface[]
     */
    public function all() : array;

    /**
     * findByMethod
     *
     * @param string $method
     *
     * @return RouteCollectionInterface
     */
    public function findByMethod(string $method) : self;

    /**
     * findByScheme
     *
     * @param mixed $scheme
     *
     * @return RouteCollectionInterface
     */
    public function findByScheme(string $scheme) : self;
}
