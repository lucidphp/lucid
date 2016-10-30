<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
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
 * @author Thomas Appel <mail@thomas-appel.com>
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

    /** @var RouteCollectionMutableInterface */
    private $routes;

    /**
     * RouteCollectionBuilder constructor.
     *
     * @param \Lucid\Mux\RouteCollectionMutableInterface|null $routes
     */
    public function __construct(RouteCollectionMutableInterface $routes = null)
    {
        $this->routes = $routes ?: $this->newRouteCollection();
        $this->groups = new SplStack;
    }

    /**
     * Returns the built RouteCollection.
     *
     * @return RouteCollectionInterface
     */
    public function getCollection() : RouteCollectionInterface
    {
        return $this->routes;
    }

    /**
     * Adds a GET route to the collection.
     *
     * @example
     * ```
     * $builder->get('/user/{id}', 'UserHandler', ['id' => '\d+'], ['id' => 1]);
     * ```
     *
     * @param array ...$args
     * @see self::addRoute()
     */
    public function get(...$args) : void
    {
        $this->addRoute('GET', ...$args);
    }

    /**
     * Adds a HEAD route to the collection.
     *
     * @example
     * ```
     * $builder->head('/user/{id}', 'UserHandler', ['id' => '\d+']);
     * ```
     *
     * @param array ...$args
     * @see self::addRoute()
     */
    public function head(...$args) : void
    {
        $this->addRoute('HEAD', ...$args);
    }

    /**
     * Adds a POST route to the collection.
     *
     * @example
     * ```
     * $builder->post('/user', 'UserHandler');
     * ```
     *
     * @param array ...$args
     * @see self::addRoute()
     */
    public function post(...$args) : void
    {
        $this->addRoute('POST', ...$args);
    }

    /**
     * Adds a PUT route to the collection.
     *
     * @example
     * ```
     * $builder->put('/user/{id}', 'UserHandler', ['id' => '\d+']);
     * ```
     *
     * @param array ...$args
     * @see self::addRoute()
     */
    public function put(...$args) : void
    {
        $this->addRoute('PUT', ...$args);
    }

    /**
     * Adds a PATCH route to the collection.
     *
     * @example
     * ```
     * $builder->patch('/user/{id}', 'UserHandler', ['id' => '\d+']);
     * ```
     *
     * @param array ...$args
     * @see self::addRoute()
     */
    public function patch(...$args) : void
    {
        $this->addRoute('PATCH', ...$args);
    }

    /**
     * Adds a DELETE route to the collection.
     *
     * @example
     * ```
     * $builder->delete('/user/{id}', 'UserHandler', ['id' => '\d+']);
     * ```
     *
     * @param array ...$args
     * @see self::addRoute()
     */
    public function delete(...$args) : void
    {
        $this->addRoute('DELETE', ...$args);
    }

    /**
     * Adds a route to the collection that handles all available request
     * methods.
     *
     * @example
     * ```
     * $builder->any('/user/{id}', 'UserHandler', ['id' => '\d+']);
     * ```
     *
     * @param string $pattern
     * @param $handler
     * @param array $req requirements
     * @param array $defaults
     */
    public function any(string $pattern, $handler, array $req = [], array $defaults = []) : void
    {
        $this->addRoute('GET|HEAD|POST|PUT|PATCH|DELETE', $pattern, $handler, $req, $defaults);
    }

    /**
     * Adds a route to the collection that handles the given request
     * methods.
     *
     * @example
     * ```
     * $builder->addRoute('GET|HEAD', '/user/{id}', 'UserHandler', ['id' => '\d+']);
     * ```
     *
     * @param string $methods methods separated by a pipe |.
     * @param string $pattern
     * @param string $handler
     * @param array  $req
     * @param array  $defaults
     */
    public function addRoute(string $methods, string $pattern, $handler, array $req = [], array $defaults = []) : void
    {
        list ($name, $host, $schemes, $constraints) = $this->parseRequirements(
            $this->extendRequirements($req),
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
     * @param callable $groupConstructor
     */
    public function group(
        string $prefix,
        array $requirements = [],
        callable $groupConstructor = null
    ) : void {
        $this->enterGroup($prefix, $requirements);

        if (null !== $groupConstructor) {
            $groupConstructor($this);
            $this->leaveGroup();
        }
    }

    /**
     * Exits the latest grouping.
     */
    public function endGroup() : void
    {
        $this->leaveGroup();
    }

    /**
     * getCollectionClass
     *
     * @return string
     */
    protected function getCollectionClass() : string
    {
        return Routes::class;
    }

    /**
     * parseRequirements
     *
     * @param array $rq
     * @param string $methods
     *
     * @return array
     */
    private function parseRequirements(array $rq, $methods) : array
    {
        $keys = [];
        $constraint = array_filter($this->mergeDefaults($rq, $methods), function ($val, $key) use (&$keys, $methods) {
            if (!in_array($key, self::$keys)) {
                return true;
            }
            $keys[$key] = $val;

            return false;
        }, ARRAY_FILTER_USE_BOTH);

        extract($keys);

        return [${self::K_NAME}, ${self::K_HOST}, ${self::K_SCHEME}, $constraint];
    }

    /**
     * mergeDefaults
     *
     * @param array $given
     * @param string $methods
     *
     * @return array
     */
    private function mergeDefaults(array $given, string $methods) : array
    {
        $defaults = array_merge(array_combine(self::$keys, array_pad([], count(self::$keys), null)), $given);

        if (null === $defaults[self::K_SCHEME]) {
            $defaults[self::K_SCHEME] = RouteInterface::DEFAULT_SCHEMES;
        }

        if (null === $defaults[self::K_NAME]) {
            $defaults[self::K_NAME] = $this->generateName($methods);
        }

        return $defaults;
    }

    /**
     * generateName
     *
     * @param string $methods
     *
     * @return string
     */
    private function generateName(string $methods) : string
    {
        return uniqid('route_' . strtr($methods, ['|' => '_']) . '_');
    }

    /**
     * newRouteCollection
     *
     * @return RouteCollectionInterface
     */
    private function newRouteCollection() : RouteCollectionInterface
    {
        $class = $this->getCollectionClass();

        if (!is_subclass_of($class, $i = RouteCollectionMutableInterface::class)) {
            throw new LogicException("RouteCollection class must implement $i.");
        }

        return new $class;
    }

    /**
     * prefixPattern
     *
     * @param mixed $pattern
     *
     * @return string
     */
    private function prefixPattern($pattern) : string
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
     * @return array
     */
    private function extendRequirements(array $requirements) : array
    {
        if (!$this->hasGroups()) {
            return $requirements;
        }

        return array_merge($this->getCurrentGroup()->getRequirements(), $requirements);
    }

    /**
     * @param $prefix
     * @param array $requirements
     *
     * @return RouteCollectionBuilder
     */
    private function enterGroup($prefix, array $requirements) : self
    {
        $group = new RouteGroup($prefix, $requirements, $this->getParentGroup());
        $this->pushGroup($group);

        return $this;
    }

    /**
     * getParentGroup
     *
     * @return RouteGroup|null
     */
    private function getParentGroup() : ?RouteGroup
    {
        return $this->hasGroups() ? $this->groups->top() : null;
    }

    /**
     * leaveGroup
     *
     * @return RouteCollectionBuilder
     */
    private function leaveGroup() : self
    {
        if ($this->hasGroups()) {
            $this->popGroup();
        }

        return $this;
    }

    /**
     * @param RouteGroup $group
     */
    private function pushGroup(RouteGroup $group) : void
    {
        $this->groups->push($group);
    }

    /**
     * popGroup
     *
     * @return RouteGroup
     */
    private function popGroup() : RouteGroup
    {
        return $this->groups->pop();
    }

    /**
     * getCurrentGroup
     *
     * @return RouteGroup
     */
    private function getCurrentGroup() : RouteGroup
    {
        return $this->groups->top();
    }

    /**
     * hasGroups
     *
     * @return bool
     */
    private function hasGroups() : bool
    {
        return $this->groups->count() > 0;
    }
}
