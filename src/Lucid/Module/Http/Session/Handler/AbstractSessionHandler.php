<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Handler;

use SessionHandlerInterface;

/**
 * @class AbstractSessionHandler
 * @see SessionHandlerInterface
 * @abstract
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractSessionHandler implements SessionHandlerInterface
{
    const DEFAULT_PREFIX = '_lucid.session_';

    /**
     * ttl
     *
     * @var int
     */
    private $ttl;

    /**
     * prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * Constructor.
     *
     * @param int $ttl time to live in minutes
     * @param string $prefix Session save prefix
     */
    public function __construct($ttl = 60, $prefix = self::DEFAULT_PREFIX)
    {
        $this->setTtl($ttl ?: 60); //default is one hour
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * getTtl
     *
     * @return int
     */
    protected function getTtl()
    {
        return $this->ttl;
    }

    /**
     * getPrefix
     *
     * @return string
     */
    protected function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * getPrefix
     *
     * @return string
     */
    protected function getPrefixed($id)
    {
        return $this->prefix.$id;
    }

    /**
     * setTtl
     *
     * @param mixed $ttl
     *
     * @return void
     */
    protected function setTtl($ttl)
    {
        if (!is_int($ttl)) {
            throw new InvalidArgumentException;
        }

        $this->ttl = $ttl * 60;
    }
}
