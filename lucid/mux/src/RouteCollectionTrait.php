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
 * Class RouteCollectionTrait
 * @api
 * @package Lucid\Mux
 * @author iwyg <mail@thomas-appel.com>
 */
trait RouteCollectionTrait
{
    /** @var array  */
    private $routes = [];

    /** @var array */
    private $methodIndex = [];

    /** @var array */
    private $schemeIndex = [];

    /**
     * @throws \DomainException
     */
    public function __clone()
    {
        throw new \DomainException('You may not clone a route collection.');
    }

    /**
     * {@inheritdoc}
     */
    public function all() : array
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name) : bool
    {
        return isset($this->routes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $routeName) : RouteInterface
    {
        if (!$this->has($routeName)) {
            throw new \LogicException(sprintf('Route "%s" does not exist.', $routeName));
        }

        return $this->routes[$routeName];
    }

    /**
     * {@inheritdoc}
     */
    public function findByMethod(string $method) : RouteCollectionInterface
    {
        return isset($this->methodIndex[($methd = strtolower($method))]) ?
            new Routes(array_intersect_key($this->routes, $this->methodIndex[$methd])) :
            new Routes([]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByScheme(string $scheme) : RouteCollectionInterface
    {
        return isset($this->schemeIndex[($scm = strtolower($scheme))]) ?
            new Routes(array_intersect_key($this->routes, $this->schemeIndex[$scm])) :
            new Routes([]);
    }
}
