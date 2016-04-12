<?php


/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache;

use Serializable;
use Lucid\Mux\RouteCollectionInterface;

/**
 * @interface CachedCollectionInterface
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CachedCollectionInterface extends RouteCollectionInterface, Serializable
{
    /**
     * Filters routes by static path and returns as a collection.
     *
     * @param string $path
     *
     * @return self
     */
    public function findByStaticPath($path);

    /**
     * Returns the time of creation as unix timestamp.
     *
     * @return int
     */
    public function getTimestamp();

    /**
     * Returns the time of creation as DateTime instance.
     *
     * @throws \RuntimeException if creation of datetime fails.
     * @return \DateTime
     */
    public function getCreationDate();
}
