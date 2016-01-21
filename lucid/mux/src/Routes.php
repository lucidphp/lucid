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
 * @class RouteCollection
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Routes implements RouteCollectionInterface
{
    /** @var array */
    private $routes;

    /** @var array */
    private $methodIndex;

    /** @var array */
    private $schemeIndex;

    /**
     * Constructor.
     *
     * @param array $routes `string[RouteInterface[]]`
     */
    public function __construct(array $routes = [])
    {
        $this->methodIndex = [];
        $this->schemeIndex = [];
        $this->setRoutes($routes);
    }

    public function all()
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function add($routeName, RouteInterface $route)
    {
        $this->routes[$routeName] = &$route;

        foreach ($route->getSchemes() as $scheme) {
            $this->schemeIndex[$scheme][] = $routeName;
        }

        foreach ($route->getMethods() as $method) {
            $this->methodIndex[$method][] = $routeName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($routeName)
    {
        if (!$this->has($routeName)) {
            return;
        }

        if (false !== $idx = array_search($routeName, $this->methodIndex[$routeName])) {
            unset($this->methodIndex[$routeName][$idx]);
            reset($this->methodIndex[$routeName]);
        }

        if (false !== $idx = array_search($routeName, $this->schemeIndex[$routeName])) {
            unset($this->schemeIndex[$routeName][$idx]);
            reset($this->schemeIndex[$routeName]);
        }

        unset($this->routes[$routeName]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return isset($this->routes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($routeName)
    {
        if (!$this->has($routeName)) {
            // throw
        }

        return $this->routes[$routeName];
    }

    /**
     * {@inheritdoc}
     */
    public function findByMethod($method)
    {
        $method = strtolower($method);

        if (!isset($this->methodIndex[$method])) {
            return new self([]);
        }

        return new self($this->methodIndex[$method]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByScheme($scheme)
    {
        $scheme = strtolower($scheme);

        if (!isset($this->schemeIndex[$scheme])) {
            return new self([]);
        }

        return new self($this->schemeIndex[$scheme]);
    }

    /**
     * Sets the initial route collection.
     *
     * @param array $routes
     *
     * @return void
     */
    private function setRoutes(array $routes)
    {
        $this->routes = [];

        foreach ($routes as $name => $route) {
            if (!is_string($name)) {
                throw new \Exception;
            }
            $this->add($name, $route);
        }
    }
}
