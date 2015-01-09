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
 * @class AbstractFtpConnection
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractFtpConnection implements FtpConnectionInterface
{
    protected $host;
    protected $port;
    protected $user;
    protected $password;
    protected $connection;

    /**
     * Constructor.
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * getConnection
     *
     * @return resource
     */
    public function getConnection()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        return $this->connection;
    }

    /**
     * setHost
     *
     * @param mixed $host
     *
     * @return void
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * setPort
     *
     * @param mixed $port
     *
     * @return void
     */
    public function setPort($port)
    {
        $this->port = (int)$port;
    }

    /**
     * setUser
     *
     * @param mixed $user
     *
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * setPassword
     *
     * @param mixed $pwd
     *
     * @return void
     */
    public function setPassword($pwd)
    {
        $this->password = empty($pwd) ? null : $pwd;
    }

    /**
     * setOptions
     *
     * @param array $options
     *
     * @return void
     */
    abstract protected function setOptions(array $options);
}
