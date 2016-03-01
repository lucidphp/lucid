<?php

/*
 * This File is part of the Lucid\Mux\Cache\Storage package
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
     * Constructor.
     *
     * @param RedisClient $redis
     * @param string $storeId
     */
    public function __construct(MemcachedClient $memcached, $storeId = self::DEFAULT_PREFIX)
    {
        $this->memcached = $memcached;
        $this->storeId   = $storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->exists()) {
            return;
        }

        $routes = $this->memcached->get($this->storeId);

        return MemcachedClient::RES_NOTFOUND === $this->memcached->getResultCode() ? null : $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes)
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
    public function isValid($time)
    {
        return $this->getLastWriteTime() < $time;
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        if (false === $this->memcached->get($this->storeId)) {
            return MemcachedClient::RES_NOTFOUND !== $this->memcached->getResultCode();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime()
    {
        if (!$this->exists()) {
            return time();
        }

        return $this->$this->memcached->get($this->storeId.'.lastmod');
    }

    /**
     * store
     *
     * @param RouteCollectionInterface $routes
     * @param mixed $method
     *
     * @return void
     */
    private function store(RouteCollectionInterface $routes, $method)
    {
        call_user_func([$this->memcached, $method, $this->storeId, $routes]);
        call_user_func([$this->memcached, $method, $this->storeId.'.lastmod', time()]);
    }
}
