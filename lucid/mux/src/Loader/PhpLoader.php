<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Cache\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Loader;

use Lucid\Mux\Routes;
use Lucid\Resource\Locator;
use Lucid\Resource\LocatorInterface;
use Lucid\Mux\RouteCollectionBuilder;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Loader\LoaderInterface;
use Lucid\Resource\Loader\AbstractFileLoader;
use Lucid\Resource\Exception\LoaderException;

/**
 * @class PhpLoader
 *
 * @package Lucid\Mux\Cache\Loader
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class PhpLoader extends AbstractFileLoader implements LoaderInterface
{
    /** @var RouteCollectionBuilder  */
    private $builder;

    /**
     * PhpLoader constructor.
     * @param RouteCollectionBuilder|null $builder
     * @param LocatorInterface|null $locator
     */
    public function __construct(RouteCollectionBuilder $builder = null, LocatorInterface $locator = null)
    {
        parent::__construct($locator ?: new Locator);
        $this->builder = $builder ?: new RouteCollectionBuilder(new Routes);
    }

    /**
     * {@inheritdoc}
     */
    public function loadRoutes($routes)
    {
        $this->load($routes);

        return $this->builder->getCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtensions()
    {
        return ['php'];
    }

    /**
     * {@inheritdoc}
     */
    protected function doLoad($file)
    {
        if (!is_array($routes = include $file)) {
            throw new LoaderException('Return value must be an array.');
        }

        $this->addRoutes($routes);

        return $this->builder->getCollection();
    }

    /**
     * loadRoutes
     *
     * @param array $routes
     *
     * @return void
     */
    private function addRoutes(array $routes)
    {
        foreach ($routes as $name => $route) {
            if (isset($route['resources'])) {
                $this->loadImports(is_array($route['resources']) ? $route['resources'] :
                (is_string($route['resources']) ? [$route['resources']] : []));
                continue;
            }

            if ((bool)$gkeys = $this->getGroupKeys($route)) {
                $req = isset($route['requirements']) ? $route['requirements'] : [];

                if (null === $prefix = isset($route['pattern']) ? $route['pattern'] : $name) {
                    continue;
                }

                foreach ($gkeys as $i) {
                    $this->loadGroup($prefix, $req, $route[$i]);
                }
                continue;
            }

            $this->addRoute($name, $route);
        }
    }

    /**
     * loadGroup
     *
     * @param string $prefix
     * @param array $requirements
     * @param array $routes
     */
    private function loadGroup($prefix, array $requirements, array $routes) : void
    {
        $this->builder->group($prefix, $requirements);
        $this->addRoutes($routes);
        $this->builder->endGroup();
    }

    /**
     * @param array $group
     * @return array
     */
    private function getGroupKeys(array $group) : array
    {
        return array_filter(array_keys($group), function ($i) {
            return is_int($i);
        });
    }

    /**
     * addRoute
     *
     * @param string $name
     * @param array $route
     */
    private function addRoute(string $name, array $route) : void
    {
        /**
         * @var string $method
         * @var string $pattern
         * @var mixed  $handler
         * @var array  $defaults
         */
        extract($this->defaults($route));

        $requirements[RouteCollectionBuilder::K_NAME] = $name;

        $this->builder->addRoute($method, $pattern, $handler, $requirements, $defaults);
    }

    /**
     * @param array $resources
     *
     * @throws \Lucid\Resource\Exception\LoaderException
     */
    private function loadImports(array $resources) : void
    {
        foreach ($resources as $resource) {
            if (!is_string($resource)) {
                continue;
            }

            $this->import($resource);
        }
    }

    /**
     * defaults
     *
     * @param array $route
     *
     * @return array
     */
    private function defaults(array $route)
    {
        return array_merge([
            'method'       => 'GET',
            'pattern'      => null,
            'handler'      => null,
            'defaults'     => [],
            'requirements' => [],
        ], $route);
    }
}
