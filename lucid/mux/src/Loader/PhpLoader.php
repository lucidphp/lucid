<?php

/*
 * This File is part of the Lucid\Mux\Cache\Loader package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Loader;

use Lucid\Mux\Routes;
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
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpLoader extends AbstractFileLoader implements LoaderInterface
{
    /**
     * Constructor.
     *
     * @param LocatorInterface $locator
     * @param RouteCollectionInterface $routes
     */
    public function __construct(LocatorInterface $locator, RouteCollectionInterface $routes = null)
    {
        parent::__construct($locator);
        $this->builder = new RouteCollectionBuilder($routes ?: new Routes);
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
            throw new LoaderException('Return value must be array.');
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
            if ((bool)$gkeys = $this->getGroupKeys($route)) {
                $req = isset($route['requirements']) ? $route['requirements'] : [];
                foreach ($gkeys as $i) {
                    $this->loadGroup($name, $req, $route[$i]);
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
     *
     * @return void
     */
    private function loadGroup($prefix, array $requirements, array $routes)
    {
        $this->builder->group($prefix, $requirements);
        $this->addRoutes($routes);
        $this->builder->endGroup();
    }
    /**
     * isGroup
     *
     * @param mixed $index
     * @param array $group
     *
     * @return void
     */
    private function getGroupKeys(array $group)
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
     *
     * @return void
     */
    private function addRoute($name, array $route)
    {
        extract($this->defaults($route));

        $requirements[RouteCollectionBuilder::K_NAME] = $name;

        $this->builder->addRoute($method, $pattern, $handler, $requirements, $defaults);
    }

    /**
     * loadImports
     *
     * @param array $routes
     *
     * @return void
     */
    private function loadImports(array $routes)
    {
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
