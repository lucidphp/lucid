<?php

/*
 * This File is part of the Lucid\Module\Http\Session package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session;

use Lucid\Module\Http\Session\Message\Flashes;
use Lucid\Module\Http\Session\Data\Attributes;
use Lucid\Module\Http\Session\Message\MessagesInterface;
use Lucid\Module\Http\Session\Data\AttributesInterface;
use Lucid\Module\Http\Session\Storage\StorageInterface;

/**
 * @class Session
 *
 * @package Lucid\Module\Http\Session
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Session implements SessionInterface
{
    protected $storage;
    protected $attrKey;
    protected $flashKey;

    /**
     * Constructor
     *
     * @param StorageInterface $store
     * @param AttributesInterface $attributes
     * @param MessageInterface $flashes
     */
    public function __construct(
        StorageInterface $store,
        AttributesInterface $attributes = null,
        MessagesInterface $flashes = null
    ) {
        $this->storage = $store;
        $attributes = $attributes ?: new Attributes;
        $flashes  = $flashes ?: new Flashes;

        $this->attrKey  = $attributes->getKey();
        $this->flashKey = $flashes->getKey();

        $this->addAttributes($flashes);
        $this->addAttributes($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->storage->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->storage->setId($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->storage->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->storage->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get($attr, $default = null)
    {
        return $this->getAttributes($this->attrKey)->get($attr, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($attr, $value)
    {
        return $this->getAttributes($this->attrKey)->set($attr, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function has($attr)
    {
        return $this->getAttributes($this->attrKey)->has($attr);
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return $this->getAttributes($this->attrKey)->keys();
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        return $this->storage->start();
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        return $this->storage->save();
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->storage->isActive();
    }

    /**
     * {@inheritdoc}
     */
    public function isClosed()
    {
        return $this->storage->isClosed();
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return $this->storage->isStarted();
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return $this->storage->close();
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($destroy = true, $ttl = null)
    {
        $this->storage->regenerate($destroy, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->storage->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaData()
    {
        return $this->storage->getMetaData();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($name)
    {
        return $this->storage->getAttributes($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttributes($name)
    {
        return $this->storage->hasAttributes($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $this->storage->setAttributes($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributes(AttributesInterface $attributes)
    {
        $this->storage->addAttributes($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->storage->getAttributes($this->flashKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($key, $message)
    {
        $this->getFlashes()->set($key, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage($key, $default = null)
    {
        $this->getFlashes()->get($key, $default = null);
    }
}
