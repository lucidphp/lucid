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

use Lucid\Mux\Cache\CachedCollectionInterface;
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

    /** @var \Redis  */
    private $redis;

    /**
     * Constructor.
     *
     * @param RedisClient $redis
     * @param string $storeId
     */
    public function __construct(RedisClient $redis = null, string $storeId = 'lucid_routes')
    {
        $this->redis = $redis ?: new RedisClient;
        $this->storeId = $storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function read() : ?CachedCollectionInterface
    {
        if (false === $routes = $this->redis->get($this->storeId)) {
            return null;
        }

        return unserialize($routes);
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes) : void
    {
        $storeArgs =
            [[$this->storeId, serialize($this->getCollection($routes))], [$this->storeId.'.lastmod', time()]];
        foreach ($storeArgs as $args) {
            $this->redis->set(...$args);
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
        return false !== $this->redis->get($this->storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime() : int
    {
        if (false !== $time = $this->redis->get($this->storeId.'.lastmod')) {
            return $time;
        }

        return time();
    }
}
