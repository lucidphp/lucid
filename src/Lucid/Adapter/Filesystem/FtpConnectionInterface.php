<?php

/*
 * This File is part of the Lucid\Adapter\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Filesystem;

/**
 * @interface FtpConnectionInterface
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface FtpConnectionInterface
{
    /**
     * connect
     *
     * @return boolean
     */
    public function connect();

    /**
     * login
     *
     * @return void
     */
    public function login();

    /**
     * close
     *
     * @return void
     */
    public function close();

    /**
     * setMountPoint
     *
     * @param string $mount
     *
     * @return void
     */
    public function setMountPoint($mount = null);

    /**
     * getConnection
     *
     * @return mixed
     */
    public function getConnection();

    /**
     * isConnected
     *
     * @return boolean
     */
    public function isConnected();
}
