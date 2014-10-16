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

use Lucid\Module\Routing\RouteCollectionInterface;

/**
 * @interface CachedCollectionInterface
 *
 * @package Lucid\Module\Routing\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CachedCollectionInterface extends RouteCollectionInterface, \Serializable
{
    public function findByStaticPath($path);
}
