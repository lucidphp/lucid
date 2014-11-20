<?php

/*
 * This File is part of the Lucid\Module\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Cache\Driver;

use Redis;
use Lucid\Module\Cache\CacheInterface;

/**
 * @class RedisDriver
 *
 * @package Lucid\Module\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RedisDriver extends AbstractDriver
{
    private $redis;

    /**
     * Constructor.
     *
     * @param redis $client
     *
     * @return void
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return false !== $this->redis->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        if (false !== ($res = $this->redis->get($key))) {
            return unserialize($res);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $data, $expires = 60, $compressed = false)
    {
        if (CacheInterface::PERSIST === $expires) {
            return $this->saveForever($key, $data, $compressed);
        }

        if ($this->redis->set($key, serialize($data))) {
            $this->redis->expireAt($key, $expires);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return 0 < $this->redis->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->redis->flushAll();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function saveForever($key, $data, $compressed = false)
    {
        if (!$this->redis->set($key, serialize($data))) {
            return false;
        }

        $this->redis->persist($key);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function parseExpireTime($expires)
    {
        return $this->expiryToUnixTimestamp($expires);
    }

    /**
     * {@inheritdoc}
     */
    protected function incrementValue($key, $value)
    {
        return $this->redis->incrBy($key, (int)$value);
    }

    /**
     * {@inheritdoc}
     */
    protected function decrementValue($key, $value)
    {
        return $this->redis->decrBy($key, (int)$value);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPersistLevel()
    {
        return CacheInterface::PERSIST;
    }
}
