<?php

/*
 * This File is part of the Lucid\Module\Http\Session\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Handler;

use Memcached;

/**
 * @class MemcachedSessionHandler
 *
 * @package Lucid\Module\Http\Session\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MemcachedSessionHandler extends AbstractSessionHandler
{
    /**
     * memcached
     *
     * @var Memcached
     */
    private $memcached;

    /**
     * Constructor.
     *
     * @param Memcached $memcached
     * @param int $ttl
     * @param string $prefix
     */
    public function __construct(Memcached $memcached, $ttl = 60, $prefix = self::DEFAULT_PREFIX)
    {
        parent::__construct($ttl, $prefix);
        $this->memcached = $memcached;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if (false !== ($res = $this->memcached->get($this->getPrefixed($sessionId)))) {
            return $res;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        return $this->memcached->set($this->getPrefixed($sessionId), $data, time() + $this->getTtl());
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return $this->memcached->delete($this->getPrefixed($sessionId));
    }
}
