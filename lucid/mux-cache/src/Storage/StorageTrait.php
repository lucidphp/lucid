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

use Lucid\Mux\Cache\Routes;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Cache\CachedCollectionInterface;

/**
 * @trait StorageTrait
 *
 * @package Lucid\Mux\Cache\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait StorageTrait
{
    /** @var string */
    private $storeId;

    /**
     * getCollection
     *
     * @param RouteCollectionInterface $routes
     *
     * @return CachedCollectionInterface
     */
    private function getCollection(RouteCollectionInterface $routes)
    {
        if (!$routes instanceof CachedCollectionInterface) {
            $routes = new Routes($routes);
        }

        return $routes;
    }
}
