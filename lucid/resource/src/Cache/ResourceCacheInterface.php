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

/**
 * @class ResourceCacheInterface
 *
 * @package Lucid\Resource\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResourceCacheInterface
{
    public function setResource(ResourceInterface $resource);

    public function getResource();

    public function isValid();
}
