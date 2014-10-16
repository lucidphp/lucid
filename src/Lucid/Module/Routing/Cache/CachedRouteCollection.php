<?php

/*
 * This File is part of the Lucid\Module\Routing\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Cache;

use Lucid\Module\Routing\RouteCollection;
use Lucid\Module\Routing\RouteCollectionInterface;

/**
 * @class CachedRouteCollection
 *
 * @package Lucid\Module\Routing\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CachedRouteCollection extends RouteCollection implements CachedCollectionInterface
{
    private $mMap;
    private $spMap;
    private $scMap;

    /**
     * Constructor.
     */
    public function __construct(RouteCollectionInterface $routes)
    {
        $this->mMap = [];
        $this->spMap = [];
        $this->scMap = [];
        $this->createMaps($routes);
        $this->routes = $routes->all();
    }

    /**
     * {@inheritdoc}
     */
    public function findByMethod($method)
    {
        return new RouteCollection(isset($this->mMap[$method]) ? $this->slice($this->mMap[$method]) : []);
    }

    /**
     * {@inheritdoc}
     */
    public function findByScheme($scheme)
    {
        return new RouteCollection(isset($this->scMap[$scheme]) ? $this->slice($this->scMap[$scheme]) : []);
    }

    /**
     * {@inheritdoc}
     */
    public function findByStaticPath($path)
    {
        return new RouteCollection(isset($this->spMap[$path]) ? $this->slice($this->spMap[$path]) : []);
    }

    /**
     * serialize
     *
     *
     * @return void
     */
    public function serialize()
    {
        return serialize([
            'sp_map' => $this->spMap,
            'sc_map' => $this->scMap,
            'm_map'  => $this->mMap,
        ]);
    }

    /**
     * unserialize
     *
     * @param string $data
     *
     * @return void
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        $this->mMap  = $data['m_map'];
        $this->scMap = $data['sc_map'];
        $this->spMap = $data['sp_map'];
    }

    /**
     * slice
     *
     * @param array $array
     *
     * @return array
     */
    private function slice(array $array)
    {
        return array_intersect_key($this->routes, array_flip($array));
    }

    /**
     * createMaps
     *
     * @param RouteCollectionInterface $routes
     *
     * @return void
     */
    private function createMaps(RouteCollectionInterface $routes)
    {
        foreach ($routes->all() as $name => $route) {
            foreach ($route->getMethods() as $method) {
                $this->mMap[$method][] = $name;
            }

            foreach ($route->getSchemes() as $scheme) {
                $this->scMap[$scheme][] = $name;
            }

            $this->spMap[$route->getContext()->getStaticPath()] = $name;
        }
    }
}
