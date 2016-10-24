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
     * @param string $storeId
     */
    public function __construct(string $storeId = 'lucid_routes')
    {
        if (!extension_loaded('apcu')) {
            throw new RuntimeException('APCu extension not loaded.');
        }

        $this->storeId = $storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function read() : ?CachedCollectionInterface
    {
        $routes = apcu_fetch($this->storeId, $success);

        return $success ? unserialize($routes) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes) : void
    {
        $storeArgs = [
            [$this->storeId, serialize($this->getCollection($routes))],
            [$this->storeId.'.lastmod', time()]
        ];

        foreach ($storeArgs as $args) {
            apcu_store(...$args);
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
        return apcu_exists($this->storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime() : int
    {
        if (!$this->exists()) {
            return time();
        }

        return apcu_fetch($this->storeId.'.lastmod');
    }
}
