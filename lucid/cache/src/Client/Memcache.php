<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Client;

use Memcache as MemcacheClient;

/**
 * @class MemcacheClient
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Memcache extends MemcachedClient
{
    /**
     * Flag stored along with cached items.
     *
     * @var int
     */
    const ITEM_EXISTS = 4;

    /**
     * Constructor.
     *
     * @param Memcache $memcache
     */
    public function __construct(MemcacheClient $memcache)
    {
        $this->driver = $memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        $flags = 0;
        $res = $this->driver->get($key, $flags);

        return false !== $res ? true : $this->itemExists($flags);
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        $flags = 0;
        $res = $this->driver->get($key, $flags);

        return $res ? $res : ($this->itemExists($flags) ? $res : null);
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $data, $expires = 60, $compressed = false)
    {
        $flags = $compressed ? MEMCACHE_COMPRESSED : 0;
        $cached = $this->driver->set($key, $data, $flags | self::ITEM_EXISTS, $expires);

        return $cached;
    }

    protected function itemExists(&$flags)
    {
        return 0 === (self::ITEM_EXISTS & ~$flags);
    }
}
