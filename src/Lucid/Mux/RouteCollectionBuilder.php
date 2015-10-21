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

use SplStack;

/**
 * @class RouteCollectionBuilder
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteCollectionBuilder
{
    /**
     * groups
     *
     * @var mixed
     */
    private $groups;

    /**
     * routes
     *
     * @var mixed
     */
    private $routes;

    /**
     * Constructor.
     */
    public function __construct(RouteCollectionInterface $routes = null)
    {
        $this->routes = $routes ?: $this->newRouteCollection();
        $this->initGroups();
    }

    /**
     * getCollection
     *
     * @return RouteCollectionInterface
     */
    public function getCollection()
    {
        return $this->routes;
    }

    /**
     * Adds a GET route to the collection.
     *
     * @param string $pattern
     * @param string $handler
     * @param array  $requirements
     * @param array  $defaults
     *
     * @return void
     */
    public function get($pattern, $handler, array $requirements = [], array $defaults = [])
    {
        $this->addRoute('GET', $pattern, $handler, $requirements, $defaults);
    }

    /**
     * Adds a HEAD route to the collection
     *
     * @see RouteCollectionBuilder#get()
     */
    public function head($pattern, $handler, array $requirements = [], array $defaults = [])
    {
        $this->addRoute('HEAD', $pattern, $handler, $requirements, $defaults);
    }

    /**
     * Adds a POST route to the collection
     *
     * @see RouteCollectionBuilder#get()
     */
    public function post($pattern, $handler, array $requirements = [], array $defaults = [])
    {
        $this->addRoute('POST', $pattern, $handler, $requirements, $defaults);
    }

    /**
     * Adds a PUT route to the collection
     *
     * @see RouteCollectionBuilder#get()
     */
    public function put($pattern, $handler, array $requirements = [], array $defaults = [])
    {
        $this->addRoute('PUT', $pattern, $handler, $requirements, $defaults);
    }

    /**
     * Adds a PATCH route to the collection
     *
     * @see RouteCollectionBuilder#get()
     */
    public function patch($pattern, $handler, array $requirements = [], array $defaults = [])
    {
        $this->addRoute('PATCH', $pattern, $handler, $requirements, $defaults);
    }

    /**
     * Adds a DELETE route to the collection
     *
     * @see RouteCollectionBuilder#get()
     */
    public function delete($pattern, $handler, array $requirements = [], array $defaults = [])
    {
        $this->addRoute('DELETE', $pattern, $handler, $requirements, $defaults);
    }

    /**
     * Adds a route to the collection that handles all available request
     * methods.
     *
     * @see RouteCollectionBuilder#get()
     */
    public function any($pattern, $handler, array $requirements = [], array $defaults = [])
    {
        $this->addRoute('GET|HEAD|POST|PUT|PATCH|DELETE', $pattern, $handler, $requirements, $defaults);
    }

    /**
     * Adds a route to the collection that handles the given request
     * methods.
     *
     * @param string $methods methods seperated by a pipe |.
     * @param string $pattern
     * @param string $handler
     * @param array  $requirements
     * @param array  $defaults
     *
     * @return void
     */
    public function addRoute($methods, $pattern, $handler, array $requirements = [], array $defaults = [])
    {
        list ($name, $host, $schemes, $constraints) = $this->parseRequirements(
            $this->extendRequirements($requirements),
            $methods
        );

        $route = new Route(
            $this->prefixPattern($pattern),
            $handler,
            $methods,
            $host,
            $defaults,
            $constraints,
            $schemes
        );

        $this->routes->add($name, $route);
    }

    /**
     * Starts a new entry point for grouping routes.
     *
     * @param string $prefix
     * @param array $requirements
     *
     * @return void
     */
    public function group($prefix, $requirements = [], $groupConstructor = null)
    {
        if (is_callable($requirements)) {
            $groupConstructor = $requirements;
            $requirements = [];
        }

        $this->enterGroup($prefix, $requirements);

        if (is_callable($groupConstructor)) {
            call_user_func($groupConstructor, $this);
            $this->leaveGroup();
        }
    }

    /**
     * endGroup
     *
     * @return void
     */
    public function endGroup()
    {
        if ($this->hasGroups()) {
            $this->leaveGroup();
        }
    }

    /**
     * parseRequirements
     *
     * @param array $requirements
     * @param string $methods
     *
     * @return array
     */
    protected function parseRequirements(array $requirements, $methods)
    {
        $name    = isset($requirements['name']) ? $requirements['name'] : $this->generateName($methods);
        $host    = isset($requirements['host']) ? $requirements['host'] : null;
        $schemes = isset($requirements['schemes']) ? $requirements['schemes'] : null;

        unset($requirements['name']);
        unset($requirements['host']);
        unset($requirements['schemes']);

        return [$name, $host, $schemes, $requirements];
    }

    /**
     * generateName
     *
     * @param string $methods
     *
     * @return string
     */
    protected function generateName($methods)
    {
        return uniqid('route_' . strtr($methods, ['|' => '_']) . '_');
    }

    /**
     * newRouteCollection
     *
     * @return RouteCollectionInterface
     */
    protected function newRouteCollection()
    {
        $class = $this->getCollectionClass();

        if (!is_subclass_of($class, $i = 'Lucid\Routing\RouteCollectionInterface')) {
            throw new \LogicException("Routecollection class must implement $i.");
        }

        return new $class;
    }

    /**
     * getCollectionClass
     *
     * @return void
     */
    protected function getCollectionClass()
    {
        return Routes::class;
    }

    /**
     * prefixPattern
     *
     * @param mixed $pattern
     *
     * @return string
     */
    protected function prefixPattern($pattern)
    {
        $d = '/';

        if (!$this->hasGroups()) {
            return $d.trim($pattern, $d);
        }

        $prefix = $this->getCurrentGroup()->getPrefix();

        return rtrim(($d === $prefix ? $prefix : (rtrim($prefix, $d).$d)) . trim($pattern, $d), $d);
    }

    /**
     * extendRequirements
     *
     * @param mixed $requirements
     *
     * @return void
     */
    protected function extendRequirements(array $requirements)
    {
        if (!$this->hasGroups()) {
            return $requirements;
        }

        return array_merge($this->getCurrentGroup()->getRequirements(), $requirements);
    }

    /**
     * enterGroup
     *
     * @return RouteBuilder
     */
    protected function enterGroup($prefix, array $requirements)
    {
        $group = new RouteGroup($prefix, $requirements, $this->getParentGroup());
        $this->pushGroup($group);

        return $this;
    }

    /**
     * getParentGroup
     *
     * @return null|GroupDefinition
     */
    protected function getParentGroup()
    {
        if ($this->hasGroups()) {
            return $this->groups->top();
        }
    }

    /**
     * leaveGroup
     *
     * @return RouteBuilder
     */
    protected function leaveGroup()
    {
        if ($this->hasGroups()) {
            $this->popGroup();
        }

        return $this;
    }

    /**
     * @return mixed
     */
    protected function initGroups()
    {
        $this->groups = new SplStack;
    }

    /**
     * pushGroup
     *
     * @param array $group
     *
     * @return void
     */
    protected function pushGroup(RouteGroup $group)
    {
        $this->groups->push($group);
    }

    /**
     * popGroup
     *
     * @return array
     */
    protected function popGroup()
    {
        return $this->groups->pop();
    }

    /**
     * getCurrentGroup
     *
     * @return mixed
     */
    protected function getCurrentGroup()
    {
        return $this->groups->top();
    }

    /**
     * hasGroups
     *
     * @return boolean
     */
    protected function hasGroups()
    {
        return $this->groups->count() > 0;
    }
}
