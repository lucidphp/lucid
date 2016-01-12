<?php

/*
 * This File is part of the Lucid\Resource\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Cache;

use Lucid\Resource\ResourceInterface;

/**
 * @class ResourceCacheInterface
 *
 * @package Lucid\Resource\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResourceCacheInterface
{
    /**
     * setResource
     *
     * @param ResourceInterface $resource
     *
     * @return void
     */
    public function setResource(ResourceInterface $resource);

    /**
     * getResource
     *
     * @return ResourceInterface
     */
    public function getResource();

    /**
     * isValid
     *
     * @return bool
     */
    public function isValid();
}
