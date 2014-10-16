<?php

/*
 * This File is part of the Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

/**
 * @interface RouteCollectionInterface
 *
 * @package Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteCollectionInterface extends \Countable
{
    /**
     * Add a route to the collection
     *
     * @param string $name
     * @param RouteInterface $route
     *
     * @return void
     */
    public function add($name, RouteInterface $route);

    /**
     * Get a route by name
     *
     * @param string $name
     *
     * @return RouteInterface|null
     */
    public function get($name);

    /**
     * Get all routes as assoc. array
     *
     * @return array
     */
    public function all();

    /**
     * count
     *
     * @return int
     */
    public function count();

    /**
     * @see RouteCollectionInterface::get()
     *
     * @param string $name
     *
     * @return RouteInterface
     */
    public function findByName($name);

    /**
     * Creates a new collection based on the method
     *
     * @param string $method
     *
     * @return RouteCollectionInterface
     */
    public function findByMethod($method);

    /**
     * Creates a new collection based on the scheme
     *
     * @param string $scheme
     *
     * @return RouteCollectionInterface
     */
    public function findByScheme($scheme);
}
