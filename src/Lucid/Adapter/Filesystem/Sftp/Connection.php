<?php

/*
 * This File is part of the Lucid\Adapter\Filesystem\Ftp package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Filesystem\Sftp;

use Net_SFTP;
use Crypt_RSA;
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
    protected $timeout;
    protected $privateKey;


    /**
     * setTimeout
     *
     * @param mixed $timeout
     *
     * @return void
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int)$timeout;
    }


    /**
     * setPrivateKey
     *
     * @param mixed $key
     *
     * @return void
     */
    public function setPrivateKey($key)
    {
        $this->privateKey = empty($key) ? null : $key;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        $this->connection = $this->connection ?: new Net_SFTP($this->host, $this->port, $this->timeout);

        try {
            $this->login();
        } catch (\Exception $e) {
            $this->close();
            throw $e;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        unset($this->connection);
        $this->connection = null;
    }

    /**
     * {@inheritdoc}
     */
    public function login()
    {
        if ($this->connection->login($u = $this->user, $pw = $this->getPassword())) {
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
            throw new \RuntimeException(sprintf('Connot mount "%s", no connection.', $mount));
        }

        if (empty($mount)) {
            return true;
        }

        if (!$this->connection->chdir($mount)) {
            throw new \RuntimeException(sprintf('Could mount "%s" on sftp connetion.', $mount));
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
        return null !== $this->connection && $this->connection instanceof Net_SFTP;
    }

    /**
     * getPassword
     *
     *
     * @return void
     */
    protected function getPassword()
    {
        if (null !== $this->privateKey && is_file($this->privateKey)) {
            return $this->getPrivateKey();
        }

        return $this->password;
    }

    /**
     * getPrivateKey
     *
     *
     * @return void
     */
    protected function getPrivateKey()
    {
        $key = new Crypt_RSA();

        if (is_file($this->privateKey)) {
            $key->loadKey(file_get_contents($this->privateKey));
        } elseif (null !== $this->password) {
            $key->setPassword($this->password);
        }


        return $key;
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
            'port' => 22,
            'user' => '',
            'password' => '',
            'passive' => false,
            'timeout' => 60,
            'private_key' => null
        ], $options);

        $this->setHost($options['host']);
        $this->setPort($options['port']);
        $this->setUser($options['user']);
        $this->setPassword($options['password']);
        $this->setPrivateKey($options['private_key']);
        $this->setTimeOut($options['timeout']);
    }
}
