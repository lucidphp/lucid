<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session\Storage;

use Lucid\Http\Session\Data\MetaData;
use Lucid\Http\Session\SessionHelperTrait;

/**
 * @class NonePersistentArrayStorate
 * @see AbstractStorage
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TransitoryArrayStorage extends AbstractStorage
{
    use SessionHelperTrait;

    private $id;
    private $name;
    private $data;

    public function __construct($name = 'ARRAYSESSION', MetaData $meta = null, array $attributes = [])
    {
        $this->setName($name);
        $this->metaData = $meta ?: new MetaData;
        $this->setAttributes($attributes);
        $data = [];
        $this->setSessionData($data);
        $this->started = false;
        $this->closed = false;
    }

    public function setSessionData(array &$data)
    {
        $this->data = &$data;
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

        return $this->started = true;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->started = false;

        $this->data = $this->mergeAttributes();

        return $this->closed = !$this->started;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($destroy = false, $ttl = true)
    {
        $this->setId($this->generateId());

        if ($destroy) {
            $this->metaData->markAsNew();
        }
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
    public function getId()
    {
        return $this->id;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return (bool)$this->started;
    }

    /**
     * {@inheritdoc}
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return !$this->closed && $this->started;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        foreach ($this->attributes as $attrs) {
            $attrs->clear();
        }

        $this->data = [];
        $this->initializeSession();
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeSession(array &$data = null)
    {
        if (null === $data) {
            $data =& $this->data;
        }

        parent::initializeSession($data);
    }
}
