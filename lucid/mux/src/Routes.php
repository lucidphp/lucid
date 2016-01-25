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
    protected $routes;

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
        if (!is_string($routeName)) {
            throw new \InvalidArgumentException('Routename must be string.');
        }

        $this->routes[$routeName] = &$route;

        foreach ($route->getSchemes() as $scheme) {
            $this->schemeIndex[$scheme][$routeName] = true;
        }

        foreach ($route->getMethods() as $method) {
            $this->methodIndex[$method][$routeName] = true;
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

        $route = $this->get($routeName);

        foreach ($route->getMethods() as $m) {
            unset($this->methodIndex[$m][$routeName]);
        }

        foreach ($route->getSchemes() as $s) {
            unset($this->schemeIndex[$s][$routeName]);
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
        return $this->routes[$routeName];
    }

    /**
     * {@inheritdoc}
     */
    public function findByMethod($method)
    {
        $method = strtoupper($method);

        if (!isset($this->methodIndex[$method])) {
            return new self([]);
        }

        return new self(array_intersect_key($this->routes, $this->methodIndex[$method]));
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

        return new self(array_intersect_key($this->routes, $this->schemeIndex[$scheme]));
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
            $this->add($name, $route);
        }
    }
}
