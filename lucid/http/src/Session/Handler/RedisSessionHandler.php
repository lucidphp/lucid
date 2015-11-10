<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session\Handler;

use Redis;

/**
 * @class RedisSessionHandler
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RedisSessionHandler extends AbstractSessionHandler
{
    /**
     * redis
     *
     * @var Redis
     */
    private $redis;

    /**
     * Constructor.
     *
     * @param Redis $redis
     * @param int $ttl
     * @param string $prefix
     */
    public function __construct(Redis $redis, $ttl = 60, $prefix = self::DEFAULT_PREFIX)
    {
        parent::__construct($ttl, $prefix);
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if (false !== ($res = $this->redis->get($this->getPrefixed($sessionId)))) {
            return $res;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        if ($this->redis->set($key = $this->getPrefixed($sessionId), $data)) {
            $this->redis->expireAt($key, time() + $this->getTtl());

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return 0 < $this->redis->delete($this->getPrefixed($sessionId));
    }
}
