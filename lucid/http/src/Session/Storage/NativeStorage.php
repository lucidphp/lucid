<?php

/*
 * This File is part of the Lucid\Http\Session\Storage package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session\Storage;

use SessionHandler;
use SessionHandlerInterface;
use Lucid\Http\Session\Data\MetaData;

/**
 * @class NativeSessionStorage
 *
 * @package Lucid\Http\Session\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class NativeStorage extends AbstractStorage
{
    /**
     * handler
     *
     * @var SessionHandlerInterface
     */
    protected $handler;

    /**
     * Constructor
     *
     * @param SessionHandlerInterface $handler
     * @param MetaData $meta
     * @param array $attributes
     */
    public function __construct(
        $name = 'PHPSESSION',
        SessionHandlerInterface $handler = null,
        MetaData $meta = null,
        array $attributes = []
    ) {
        $this->setName($name);
        $this->metaData = $meta ?: new MetaData;
        $this->setAttributes($attributes);
        $this->setSessionHandler($handler ?: new SessionHandler);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        session_id($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        session_name($name);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->started && !$this->closed) {
            return false;
        }

        if (PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('The session was already started by PHP.');
        }

        if (session_start()) {
            return $this->initializeSession();
        }

        throw new \RuntimeException('Cannot start session.');
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($destroy = false, $ttl = null)
    {
        if (!$ret = session_regenerate_id($destroy)) {
            return false;
        }

        // set the new ttl value.
        if (null !== $ttl) {
            ini_set('session.cookie_lifetime', (int)$ttl);
        }

        // mark the meta data as new
        if ($destroy) {
            $this->metaData->markAsNew();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        session_write_close();
        $this->started = false;

        return $this->closed = true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        foreach ($this->attributes as $attribute) {
            $attribute->clear();
        }

        $_SESSION = [];

        return $this->initializeSession();
    }

    /**
     * isActive
     *
     * @return void
     */
    public function isActive()
    {
        return $this->started && !$this->closed && PHP_SESSION_ACTIVE === session_status();
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return $this->isActive();
    }

    /**
     * {@inheritdoc}
     */
    public function isClosed()
    {
        return $this->closed && !$this->started && PHP_SESSION_ACTIVE !== session_status();
    }

    /**
     * getHandler
     *
     * @return SessionHandlerInterface
     */
    protected function getHandler()
    {
        return $this->handler;
    }

    /**
     * initializeSession
     *
     * @param array $session
     *
     * @return void
     */
    protected function initializeSession(array &$data = null)
    {
        if (null === $data) {
            $data = &$_SESSION;
        }

        return parent::initializeSession($data);
    }

    /**
     * setSessionHandler
     *
     * @param SessionHandlerInterface $handler
     *
     * @return void
     */
    protected function setSessionHandler(SessionHandlerInterface $handler)
    {
        $this->handler = $handler;

        session_set_save_handler($this->handler, false);
        session_register_shutdown();
    }
}
