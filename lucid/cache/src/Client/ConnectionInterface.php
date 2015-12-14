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

/**
 * @interface ConnectionInterface
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ConnectionInterface
{
    /**
     * Connect to a server.
     *
     * @return boolean `TRUE` on success, `FALSE` on failure.
     */
    public function connect();

    /**
     * Close connection to a server.
     *
     * @return boolean `TRUE` on success, `FALSE` on failure.
     */
    public function close();

    /**
     * Tell if server is connected.
     *
     * @return boolean `TRUE` if connected, otherwise `FALSE`.
     */
    public function isConnected();

    /**
     * Get the specified server driver.
     *
     * @return mixed
     */
    public function getClient();

    /**
     * Get the specified server driver and connect.
     *
     * @return mixed
     */
    public function getClientAndConnect();
}
