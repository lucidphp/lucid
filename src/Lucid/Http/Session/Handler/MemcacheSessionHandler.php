<?php

/*
 * This File is part of the Lucid\Http\Session\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session\Handler;

use Memcache;

/**
 * @class MemcachedSessionHandler
 *
 * @package Lucid\Http\Session\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MemcacheSessionHandler extends AbstractSessionHandler
{
    /**
     * memcache
     *
     * @var Memcache
     */
    private $memcache;

    /**
     * Constructor.
     *
     * @param Memcached $memcached
     * {@inheritdoc}
     */
    public function __construct(Memcache $memcache, $ttl = 60, $prefix = self::DEFAULT_PREFIX)
    {
        parent::__construct($ttl, $prefix);
        $this->memcache = $memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if (false !== ($res = $this->memcache->get($this->getPrefixed($sessionId)))) {
            return $res;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        return $this->memcache->set(
            $this->getPrefixed($sessionId),
            $data,
            Memcache::MEMCACHE_COMPRESSED,
            time() + $this->getTtl()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return $this->memcache->delete($this->getPrefixed($sessionId));
    }
}
