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

use RuntimeException;
use Lucid\Mux\Cache\StorageInterface;
use Lucid\Mux\RouteCollectionInterface;

/**
 * @class Apcu
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Apcu implements StorageInterface
{
    use StorageTrait;

    /**
     * Constructor.
     *
     * @param RedisClient $redis
     * @param string $storeId
     */
    public function __construct($storeId = 'lucid_routes')
    {
        if (!extension_loaded('apcu')) {
            throw new RuntimeException('APCu extension not loaded.');
        }
        $this->storeId = $storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $routes = apcu_fetch($this->storeId, $success);

        return $success ? unserialize($routes) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes)
    {
        apcu_store($this->storeId, serialize($this->getCollection($routes)));
        apcu_store($this->storeId.'.lastmod', time());
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
        return apcu_exists($this->storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime()
    {
        if (!$this->exists()) {
            return time();
        }

        return apcu_fetch($this->storeId.'.lastmod');
    }
}
