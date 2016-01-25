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
use LogicException;

/**
 * @class RouteCollectionBuilder
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteCollectionBuilder
{
    /** @var string */
    const K_NAME = 'route';

    /** @var string */
    const K_HOST = 'host';

    /** @var string */
    const K_SCHEME = 'schemes';

    /** @var array */
    private static $keys = [self::K_NAME, self::K_HOST, self::K_SCHEME];

    /** @var array */
    private $groups;

    /** @var RouteContextInterface */
    private $routes;

    /**
     * Constructor.
     */
    public function __construct(RouteCollectionInterface $routes = null)
    {
        $this->routes = $routes ?: $this->newRouteCollection();
        $this->groups = new SplStack;
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
            is_array($methods) ? $methods : explode('|', $methods),
            $host,
            $defaults,
            $constraints,
            is_array($schemes) ? $schemes : explode('|', $schemes)
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
    public function group($prefix, array $requirements = [], callable $groupConstructor = null)
    {
        $this->enterGroup($prefix, $requirements);

        if (null !== $groupConstructor) {
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
        $this->leaveGroup();
    }

    /**
     * parseRequirements
     *
     * @param array $requirements
     * @param string $methods
     *
     * @return array
     */
    private function parseRequirements(array $rq, $methods)
    {
        $keys = [];
        $constr = array_filter($this->mergeDefaults($rq), function ($val, $key) use (&$keys, $methods) {
            if (!in_array($key, self::$keys)) {
                return true;
            }
            if (self::K_NAME === $key && null === $val) {
                $val = $this->generateName($methods);
            }
            $keys[$key] = $val;
            return false;
        }, ARRAY_FILTER_USE_BOTH);

        extract($keys);

        return [${self::K_NAME}, ${self::K_HOST}, ${self::K_SCHEME}, $constr];
    }

    /**
     * mergeDefaults
     *
     * @param array $given
     *
     * @return array
     */
    private function mergeDefaults(array $given)
    {
        return array_merge(array_combine(self::$keys, array_pad([], count(self::$keys), null)), $given);
    }

    /**
     * generateName
     *
     * @param string $methods
     *
     * @return string
     */
    private function generateName($methods)
    {
        return uniqid('route_' . strtr($methods, ['|' => '_']) . '_');
    }

    /**
     * newRouteCollection
     *
     * @return RouteCollectionInterface
     */
    private function newRouteCollection()
    {
        $class = $this->getCollectionClass();

        if (!is_subclass_of($class, $i = __NAMESPACE__.'\RouteCollectionInterface')) {
            throw new LogicException("Routecollection class must implement $i.");
        }

        return new $class;
    }

    /**
     * getCollectionClass
     *
     * @return void
     */
    private function getCollectionClass()
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
    private function prefixPattern($pattern)
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
    private function extendRequirements(array $requirements)
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
    private function enterGroup($prefix, array $requirements)
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
    private function getParentGroup()
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
    private function leaveGroup()
    {
        if ($this->hasGroups()) {
            $this->popGroup();
        }

        return $this;
    }

    /**
     * pushGroup
     *
     * @param array $group
     *
     * @return void
     */
    private function pushGroup(RouteGroup $group)
    {
        $this->groups->push($group);
    }

    /**
     * popGroup
     *
     * @return RouteGroup
     */
    private function popGroup()
    {
        return $this->groups->pop();
    }

    /**
     * getCurrentGroup
     *
     * @return RouteGroup
     */
    private function getCurrentGroup()
    {
        return $this->groups->top();
    }

    /**
     * hasGroups
     *
     * @return bool
     */
    private function hasGroups()
    {
        return $this->groups->count() > 0;
    }
}
