<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Cache;

use Memcached;
use Lucid\Module\Filesystem\Driver\DriverInterface;

/**
 * @class MemcachedCache
 *
 * @package Lucid\Module\Filesystem\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MemcachedCache extends AbstractCache
{
    public function __construct(DriverInterface $driver, Memcached $memcached)
    {
        parent::__construct($driver);
    }

    public function hasPathInfo($path)
    {
        if (false === $res = $this->memcached->get($path)) {
            return Memcached::RES_NOT_FOUND !== $this->memcached->getResultCode();
        }

        return true;
    }

    public function getPathInfo($path)
    {
        return $this->memcached($path);
    }

    public function updateFileObject($path, array $data)
    {
    }

    public function removeFileObject($path, array $data)
    {
    }
}
