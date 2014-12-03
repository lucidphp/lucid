<?php

/*
 * This File is part of the Lucid\Module\Http\Session\Storage package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Storage;

use SessionHandler;
use SessionHandlerInterface;

/**
 * @class NativeSessionStorage
 *
 * @package Lucid\Module\Http\Session\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SessionStorage implements SessionStorage
{
    protected $started;

    /**
     * handler
     *
     * @var SessionHandlerInterface
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param SessionHandlerInterface $handler
     */
    public function __construct(SessionHandlerInterface $handler = null, array $attributes = [])
    {
        $this->setSesssionHandler($handler ?: new SessionHandler);
    }

    public function start()
    {
    }

    public function getId()
    {
        return session_id();
    }

    public function setId($id)
    {
        session_id($id);
    }

    public function getName()
    {
        return session_name();
    }

    public function setName($name)
    {
        session_name($name);
    }

    public function regenerate($destroy = false, $ttl = null)
    {
        session_regenerate_id($destroy);
    }

    protected function isActive()
    {

    }

    protected function setSessionHandler(SessionHandlerInterface $handler)
    {
        $this->handler = $handler;
    }
}
