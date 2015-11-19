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

    public function __construct($name = null)
    {
        $this->originalName = new EventName($this, $name);
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
    final public function stop()
    {
        $this->isStopped = true;
    }

    /**
     * {@inheritdoc}
     */
    final public function isStopped()
    {
        return $this->isStopped;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = new EventName($this, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name ?: $this->originalName;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }
}
