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
     * add
     *
     * @param string $routeName
     * @param RouteInterface $route
     *
     * @return void
     */
    public function add($routeName, RouteInterface $route);

    /**
     * Remove a route by its name.
     *
     * @param string $routeName
     *
     * @return void
     */
    public function remove($routeName);

    /**
     * Get a route by name.
     *
     * @param string $routeName the name of the route.
     *
     * @return Route
     */
    public function get($routeName);

    /**
     * Should check if a route exists with the given name.
     *
     * @param string $routeName the name of the route.
     *
     * @return bool
     */
    public function has($routeName);

    /**
     * Get all registered routes as array.
     *
     * @return [string => RouteInterface]
     */
    public function all();

    /**
     * findByMethod
     *
     * @param string $method
     *
     * @return RouteCollectionInterface
     */
    public function findByMethod($method);

    /**
     * findByScheme
     *
     * @param mixed $scheme
     *
     * @return RouteCollectionInterface
     */
    public function findByScheme($scheme);
}
