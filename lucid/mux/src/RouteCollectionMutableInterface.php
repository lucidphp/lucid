<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: malcolm
 * Date: 12.10.16
 * Time: 00:09
 */

namespace Lucid\Mux;

interface RouteCollectionMutableInterface extends RouteCollectionInterface
{
    /**
     * Add a route to the collection.
     *
     * @param string $routeName
     * @param RouteInterface $route
     *
     * @return void
     */
    public function add(string $routeName, RouteInterface $route) : void;

    /**
     * Remove a route by its name.
     *
     * @param string $routeName
     *
     * @return void
     */
    public function remove(string $routeName) : void;
}
