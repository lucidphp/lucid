<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

/**
 * @class RouteCollection
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteCollection implements RouteCollectionInterface
{
    protected $routes = [];

    /**
     * Constructor.
     *
     * @param array $routes
     */
    public function __construct(array $routes = [])
    {
        $this->setRoutes($routes);
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, RouteInterface $route)
    {
        $this->routes[$name] = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return $this->findByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByMethod($method)
    {
        return new self(array_filter($this->routes, function ($route) use ($method) {
            return $route->hasMethod($method);
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function findByScheme($scheme)
    {
        return new self(array_filter($this->routes, function ($route) use ($scheme) {
            return $route->hasScheme($scheme);
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * setRoutes
     *
     * @param array $routes
     *
     * @return void
     */
    protected function setRoutes(array $routes)
    {
        foreach ($routes as $name => $route) {
            $this->add($name, $route);
        }
    }
}
