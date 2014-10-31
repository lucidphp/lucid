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

/**
 * @interface RouteCacheInterface
 *
 * @package Lucid\Module\Routing\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteCacheInterface
{
    public function read();
    public function write(RouteCollectionInterface $routes);
    public function isValid();
    public function exists();
    public function getLastWriteTime();
}
