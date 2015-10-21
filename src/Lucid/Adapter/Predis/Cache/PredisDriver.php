<?php

/*
 * This File is part of the Lucid\Adapter\Predis\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Adapter\Predis\Cache;

use Predis\Client;

/**
 * @class RedisDriver
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PredisDriver extends AbstractDriver
{
    private $client;

    /**
     * Constructor.
     *
     * @param Client $client
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        if (false === $this->get($key)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        if (null !== ($res = $this->client->get($key))) {
            return $res;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $data, $expires = 60, $compressed = false)
    {
        $this->client->set($key, $data);
        $this->client->expire($key, $expires);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->client->del($key);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->client->flushall();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function saveForever($key, $data, $compressed = false)
    {
        $this->set($key, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function incrementValue($key, $value)
    {
        return $this->client->incrby($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    protected function decrementValue($key, $value)
    {
        return $this->client->decrby($key, $value);
    }
}
