<?php

/*
 * This File is part of the Selene\Module\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Selene\Module\Cache\Client;

use Memcached;
use RuntimeException;

/**
 * @class MemcachedConnection
 *
 * @package Selene\Module\Cache\Client
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class MemcachedConnection implements ConnectionInterface
{
    /**
     * memcached
     *
     * @var Memcached
     */
    private $memcached;

    /**
     * servers
     *
     * @var array
     */
    private $servers;

    /**
     * Constructor.
     *
     * @param array $servers
     * @param Memcached $memcached
     */
    public function __construct(array $servers, Memcached $memcached = null)
    {
        $this->servers   = $servers;
        $this->memcached = $memcached ?: new Memcached;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->isConnected()) {
            return false;
        }

        $this->memcached->addServers($this->servers);

        if (!$this->isConnected()) {
            throw new RuntimeException('Cannot initialize Memcached');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        // quit doesn't always close the connection
        $this->memcached->quit();

        return $this->isConnected() ? false : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return (bool)$this->memcached->getVersion();
    }

    /**
     * {@inheritdoc}
     *
     * @return Memcached
     */
    public function getClient()
    {
        return $this->memcached;
    }

    /**
     * {@inheritdoc}
     *
     * @return Memcached
     */
    public function getClientAndConnect()
    {
        $this->connect();

        return $this->getClient();
    }
}
