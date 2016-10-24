<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Storage;

use Memcached as MemcachedClient;
use Lucid\Mux\Cache\StorageInterface;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Cache\CachedCollectionInterface;

/**
 * @class Memcached
 *
 * @package Lucid\Mux\Cache\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Memcached implements StorageInterface
{
    use StorageTrait;

    /** @var \Memcached */
    private $memcached;

    /**
     * Memcached constructor.
     *
     * @param \Memcached $memcached
     * @param mixed $storeId
     */
    public function __construct(MemcachedClient $memcached, string $storeId = self::DEFAULT_PREFIX)
    {
        $this->memcached = $memcached;
        $this->storeId   = $storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function read() : ?CachedCollectionInterface
    {
        if (!$this->exists()) {
            return null;
        }

        $routes = $this->memcached->get($this->storeId);

        return MemcachedClient::RES_SUCCESS !== $this->memcached->getResultCode() ? null : $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes) : void
    {
        $routes = $this->getCollection($routes);

        if ($this->exists()) {
            $this->store($routes, 'replace');
        } else {
            $this->store($routes, 'set');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(int $time) : bool
    {
        return $this->getLastWriteTime() < $time;
    }

    /**
     * {@inheritdoc}
     */
    public function exists() : bool
    {
        if (false === $this->memcached->get($this->storeId)) {
            return MemcachedClient::RES_SUCCESS === $this->memcached->getResultCode();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime() : int
    {
        if (!$this->exists()) {
            return time();
        }

        return $this->memcached->get($this->storeId.'.lastmod');
    }

    /**
     * store
     *
     * @param RouteCollectionInterface $routes
     * @param string $method
     *
     * @return void
     */
    private function store(RouteCollectionInterface $routes, string $method) : void
    {
        foreach ([[$this->storeId, $routes], [$this->storeId.'.lastmod', time()]] as $args) {
            $this->memcached->{$method}(...$args);
        }
    }
}
