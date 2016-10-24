<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache;

use Lucid\Mux\RouteCollectionTrait;
use RuntimeException;
use DateTimeImmutable;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Routes as RouteCollection;

/**
 * @class Routes
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Routes implements CachedCollectionInterface
{
    use RouteCollectionTrait;

    /** @var array */
    private $methodMap;

    /** @var array */
    private $sPathMap;

    /** @var array */
    private $schemaMap;

    /** @var int */
    private $timestamp;

    /**
     * Constructor.
     *
     * @var RouteCollectionInterface $routes
     */
    public function __construct(RouteCollectionInterface $routes)
    {
        $this->methodMap  = [];
        $this->schemaMap  = [];
        $this->sPathMap   = [];
        
        $this->routes = $routes->all();
        $this->createMaps($routes);
        $this->timestamp  = time();
    }

    /**
     * {@inheritdoc}
     */
    public function findByMethod(string $method) : RouteCollectionInterface
    {
        return new RouteCollection($this->slice($this->methodMap[$method] ?? []));
    }

    /**
     * {@inheritdoc}
     */
    public function findByScheme(string $scheme) : RouteCollectionInterface
    {
        return new RouteCollection($this->slice($this->schemaMap[$scheme] ?? []));
    }

    /**
     * {@inheritdoc}
     */
    public function findByStaticPath(string $path) : RouteCollectionInterface
    {
        return new RouteCollection($this->slice($this->sPathMap[$path] ?? []));
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationDate() : \DateTimeInterface
    {
        if ($date = (new DateTimeImmutable())->setTimestamp($this->getTimestamp())) {
            return $date;
        }

        throw new RuntimeException();
    }

    /**
     * serialize
     *
     * @return string
     */
    public function serialize() : string
    {
        return serialize([
            'sp_map'    => $this->sPathMap,
            'sc_map'    => $this->schemaMap,
            'm_map'     => $this->methodMap,
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
    public function unserialize($data) : void
    {
        $data = unserialize($data);

        $this->methodMap = $data['m_map'];
        $this->schemaMap = $data['sc_map'];
        $this->sPathMap  = $data['sp_map'];
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
    private function createMaps(RouteCollectionInterface $routes) : void
    {
        foreach ($routes->all() as $name => $route) {
            foreach ($route->getMethods() as $method) {
                $this->methodMap[$method][] = $name;
            }

            foreach ($route->getSchemes() as $scheme) {
                $this->schemaMap[$scheme][] = $name;
            }

            $this->sPathMap[$route->getContext()->getStaticPath()][] = $name;
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
        if (empty($array)) {
            return $array;
        }

        return array_intersect_key($this->routes, array_flip($array));
    }
}
