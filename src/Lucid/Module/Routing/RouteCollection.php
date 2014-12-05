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
 * @package lucid/routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteCollection implements RouteCollectionInterface
{
    /**
     * The routes.
     *
     * @var array
     */
    protected $routes;

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
        return new static(array_filter($this->routes, function ($route) use ($method) {
            return $route->hasMethod($method);
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function findByScheme($scheme)
    {
        return new static(array_filter($this->routes, function ($route) use ($scheme) {
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
     * Set Routes from a given array of routes
     *
     * @param array $routes
     *
     * @return void
     */
    protected function setRoutes(array $routes)
    {
        $this->routes = [];

        foreach ($routes as $name => $route) {
            $this->add($name, $route);
        }
    }
}
