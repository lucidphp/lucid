<?php

/*
 * This File is part of the Lucid\Filesystem\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Cache;

/**
 * @class NoopCache
 *
 * @package Lucid\Filesystem\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class NoopCache implements CacheInterface
{
    public function init()
    {
    }

    public function save()
    {
    }

    public function has($path)
    {
        return false;
    }

    public function exists($path)
    {
        return false;
    }

    public function put($path, $object, $complete = null)
    {
    }
}
