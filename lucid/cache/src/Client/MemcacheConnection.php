<?php

/*
 * This File is part of the Selene\Module\Cache\Client package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Client;

use Memcache;
use RuntimeException;

/**
 * @class MemcacheConnection
 *
 * @package Selene\Module\Cache\Client
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class MemcacheConnection implements ConnectionInterface
{
    /**
     * memcached
     *
     * @var Memcached
     * @access private
     */
    private $memcached;

    /**
     * Connection Status
     *
     * @var boolean
     */
    private $connected;

    /**
     * Constructor.
     *
     * @param array $servers
     * @param Memcached $memcached
     */
    public function __construct(array $servers, Memcache $memcache = null)
    {
        $this->memcache = $memcache ?: new Memcache;
        $this->servers = $servers;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->isConnected()) {
            return false;
        }

        $this->addServers();

        try {
            $this->memcache->getVersion();
        } catch (\Exception $e) {
            throw new RuntimeException('Cannot initialize Memcache: ' . $e->getMessage());
        }

        return $this->connected = true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->connected = false;

        return $this->memcache->close();
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return (bool)$this->connected;
    }

    /**
     * {@inheritdoc}
     *
     * @return Memcache
     */
    public function getClient()
    {
        return $this->memcache;
    }

    /**
     * {@inheritdoc}
     *
     * @return Memcache
     */
    public function getClientAndConnect()
    {
        $this->connect();

        return $this->getClient();
    }

    /**
     * Adds given server connections to memcache.
     *
     * @return void
     */
    protected function addServers()
    {
        foreach ($this->servers as $server) {
            $this->memcache->addServer($server['host'], (int)$server['port'], true, (int)$server['weight']);
        }
    }
}
