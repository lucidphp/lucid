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

/**
 * @class RouteCollection
 *
 * @package Lucid\Mux
 * @author Thomas Appel <mail@thomas-appel.com>
 */
final class Routes implements RouteCollectionMutableInterface
{
    use RouteCollectionTrait;

    /**
     * Routes constructor.
     * @param array<string, RouteInterface> $routes
     */
    public function __construct(array $routes = [])
    {
        $this->setRoutes($routes);
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $routeName, RouteInterface $route) : void
    {
        $this->routes[$routeName] = &$route;


        foreach ($route->getMethods() as $method) {
            $this->methodIndex[strtolower($method)][$routeName] = true;
        }

        foreach ($route->getSchemes() as $scheme) {
            $this->schemeIndex[strtolower($scheme)][$routeName] = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $routeName) : void
    {
        if (!$this->has($routeName)) {
            return;
        }

        $route = $this->get($routeName);

        foreach ($route->getMethods() as $m) {
            unset($this->methodIndex[strtolower($m)][$routeName]);
        }

        foreach ($route->getSchemes() as $s) {
            unset($this->schemeIndex[strtolower($s)][$routeName]);
        }

        unset($this->routes[$routeName]);
    }

    /**
     * Sets the initial route collection.
     *
     * @param RouteInterface[] $routes
     *
     * @return void
     */
    private function setRoutes(array $routes) : void
    {
        $this->routes = [];

        foreach ($routes as $name => $route) {
            $this->add($name, $route);
        }
    }
}
