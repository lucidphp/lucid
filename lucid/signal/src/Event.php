<?php

/*
 * This File is part of the Lucid\Signal package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Signal;

/**
 * @class Event
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Event implements EventInterface
{
    /** @var EventName */
    private $originalName;

    /** @var EventName */
    private $name;

    /** @var bool */
    private $isStopped = false;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->originalName = new EventName($this, (string)$name);
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->isStopped = false;
    }

    /**
     * {@inheritdoc}
     */
    final public function stop() : void
    {
        $this->isStopped = true;
    }

    /**
     * {@inheritdoc}
     */
    final public function isStopped() : bool
    {
        return $this->isStopped;
    }

    /**
     * {@inheritdoc}
     */
    final public function setName($name) : void
    {
        $this->name = new EventName($this->getEventInstance(), $name);
    }

    /**
     * {@inheritdoc}
     */
    final public function getName() : EventName
    {
        return $this->name ?: $this->originalName;
    }

    /**
     * {@inheritdoc}
     */
    final public function getOriginalName() : EventName
    {
        return $this->originalName;
    }

    /**
     * @return EventInterface
     */
    protected function getEventInstance() : EventInterface
    {
        return $this;
    }
}
