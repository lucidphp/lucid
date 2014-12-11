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

use SessionHandlerInterface;
use Lucid\Module\Http\Session\Data\MetaData;
use Lucid\Module\Http\Session\SessionHelperTrait;

/**
 * @class PhpStorage
 *
 * @package Lucid\Module\Http\Session\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpStorage extends NativeStorage
{
    use SessionHelperTrait;

    /**
     * id
     *
     * @var string
     */
    protected $id;

    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * data
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor.
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
        $this->data = [];
        parent::__construct($name, $handler, $meta, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->started && !$this->closed) {
            return false;
        }

        if (null === $this->id) {
            $this->id = $this->generateId();
        }

        $this->data = $this->getDataFromHandler();

        return $this->initializeSession();
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($destroy = false, $ttl = null)
    {
        $this->setId($this->generateId());

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
        $this->handler->write($this->getId(), serialize($this->mergeAttributes()));

        $this->started = false;

        return $this->closed = !$this->started;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        foreach ($this->attributes as $attribute) {
            $attribute->clear();
        }

        $this->data = [];
        $this->initializeSession();
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->started && !$this->closed;
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
        return $this->closed && !$this->started;
    }

    /**
     * {@inheritdoc}
     */
    protected function setSessionHandler(SessionHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * getDataFromHandler
     *
     * @return void
     */
    protected function getDataFromHandler()
    {
        $data = $this->getHandler()->read($this->getId());

        return unserialize($data) ?: [];
    }

    /**
     * mergeAttributes
     *
     * @return void
     */
    protected function mergeAttributes()
    {
        $attrs = [];

        foreach ($this->attributesAll() as $attributes) {
            $attrs[$attributes->getKey()] = $attributes->all();
        }

        return $attrs;
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
            $data = &$this->data;
        }

        return parent::initializeSession($data);
    }
}
