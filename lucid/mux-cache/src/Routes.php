<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache;

use DateTime;
use LogicException;
use RuntimeException;
use Lucid\Mux\RouteInterface;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Routes as RouteCollection;

/**
 * @class Routes
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Routes extends RouteCollection implements CachedCollectionInterface
{
    /** @var array */
    private $mMap;

    /** @var array */
    private $spMap;

    /** @var array */
    private $scMap;

    /** @var int */
    private $timestamp;

    /**
     * Constructor.
     *
     * @var RouteCollectionInterface $routes
     */
    public function __construct(RouteCollectionInterface $routes)
    {
        $this->mMap   = [];
        $this->spMap  = [];
        $this->scMap  = [];
        $this->routes = $routes->all();
        $this->createMaps($routes);
        $this->timestamp = time();
    }

    /**
     * {@inheritdoc}
     */
    public function add($routeName, RouteInterface $route)
    {
        throw new LogicException('Can\'t add routes to a cached collection.');
    }

    /**
     * {@inheritdoc}
     */
    public function remove($routeName)
    {
        throw new LogicException('Can\'t remove routes from a cached collection.');
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

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getCreationDate()
    {
        if ($date = (new DateTime)->setTimestamp($this->getTimestamp())) {
            return $date;
        }

        throw new RuntimeException();
    }

    /**
     * serialize
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            'sp_map'    => $this->spMap,
            'sc_map'    => $this->scMap,
            'm_map'     => $this->mMap,
            'routes'    => $this->routes,
            'timestamp' => $this->timestamp
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

        $this->mMap      = $data['m_map'];
        $this->scMap     = $data['sc_map'];
        $this->spMap     = $data['sp_map'];
        $this->routes    = $data['routes'];
        $this->timestamp = $data['timestamp'];
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

            $this->spMap[$route->getContext()->getStaticPath()][] = $name;
        }
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
}
