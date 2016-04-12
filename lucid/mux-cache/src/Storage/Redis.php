<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Storage;

use Redis as RedisClient;
use Lucid\Mux\Cache\StorageInterface;
use Lucid\Mux\RouteCollectionInterface;

/**
 * @class Redis
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Redis implements StorageInterface
{
    use StorageTrait;

    /**
     * Constructor.
     *
     * @param RedisClient $redis
     * @param string $storeId
     */
    public function __construct(RedisClient $redis = null, $storeId = 'lucid_routes')
    {
        $this->redis = $redis ?: new RedisClient;
        $this->storeId = $storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (false === $routes = $this->redis->get($this->storeId)) {
            return;
        }

        return unserialize($routes);
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes)
    {
        $this->redis->set($id, serialize($this->getCollection($routes)));
        $this->redis->set($id.'.lastmod', time());
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
        return false !== $this->redis->get($this->storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime()
    {
        if (false !== $time = $this->redis->get($this->storeId.'.lastmod')) {
            return $time;
        }

        return time();
    }
}
