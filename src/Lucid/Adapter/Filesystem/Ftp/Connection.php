<?php

/*
 * This File is part of the Lucid\Adapter\Filesystem\Ftp package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Filesystem\Ftp;

use Lucid\Adapter\Filesystem\AbstractFtpConnection;

/**
 * @class Connection
 *
 * @package Lucid\Adapter\Filesystem\Ftp
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Connection extends AbstractFtpConnection
{
    protected $ssl;
    protected $passive;

    /**
     * setPassive
     *
     * @param mixed $passive
     *
     * @return void
     */
    public function setPassive($passive)
    {
        $this->passive = (bool)$passive;
    }

    /**
     * setSsl
     *
     * @param mixed $ssl
     *
     * @return void
     */
    public function setSsl($ssl)
    {
        $this->ssl = (bool)$ssl;
    }

    /**
     * Colse an open FTP Buffer.
     *
     * @return boolean returns true if the buffer was open, otherwise false.
     */
    public function close()
    {
        if ($this->isConnected()) {
            ftp_close($this->connection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if (false === $connection = $this->ssl ?
            ftp_ssl_connect($this->host, $this->port) :
            ftp_connect($this->host, $this->port)
        ) {
            throw new \RuntimeException(
                sprintf('Could not establish connection using %s:%s', $this->host, $this->port)
            );
        }

        $this->connection = $connection;

        try {
            $this->login();
            $this->setPassiveMode($this->passive);
        } catch (\Exception $e) {
            $this->close();
            throw $e;
        }

        return true;
    }

    /**
     * login
     *
     * @param resource $connection
     *
     * @return boolean
     */
    public function login()
    {
        if (false !== @ftp_login($this->connection, $u = $this->user, $pw = $this->password)) {
            return true;
        }

        throw new \RuntimeException(
            sprintf('Could not log in user %s using password: %s.', $u, 0 !== strlen($pw) ? 'yes' : 'no')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setMountPoint($mount = null)
    {
        if (!$this->isConnected()) {
            throw new \RuntimeException(sprintf('Connot mount "%s" on ftp, no connection.', $mount));
        }

        if (empty($mount)) {
            return true;
        }

        if (false === @ftp_chdir($this->connection, $mount)) {
            throw new \RuntimeException(sprintf('Could mount ftp driver to "%s".', $mount));
        }

        return true;
    }

    /**
     * isConnected
     *
     * @return boolean
     */
    public function isConnected()
    {
        return is_resource($this->connection) && 'ftp buffer' === strtolower(get_resource_type($this->connection));
    }

    /**
     * setPassiveMode
     *
     * @param mixed $connection
     * @param mixed $passive
     *
     * @return boolean
     */
    protected function setPassiveMode($passive)
    {
        if (false !== $mode = ftp_pasv($this->connection, $passive)) {
            return $mode;
        }

        throw new \RuntimeException(
            sprintf('Could not set passive mode for %s:%s.', $this->host, $this->port)
        );
    }

    /**
     * setOptions
     *
     * @param array $options
     *
     * @return void
     */
    protected function setOptions(array $options)
    {
        $options = array_merge([
            'host' => '',
            'port' => 21,
            'user' => '',
            'password' => '',
            'ssl' => true,
            'passive' => false,
        ], $options);

        $this->setHost($options['host']);
        $this->setPort($options['port']);
        $this->setUser($options['user']);
        $this->setPassword($options['password']);
        $this->setSsl($options['ssl']);
        $this->setPassive($options['passive']);
    }
}
