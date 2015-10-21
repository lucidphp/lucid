<?php

/*
 * This File is part of the Lucid\Event package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Event;

/**
 * @class Event
 *
 * @package Lucid\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Event implements EventInterface
{
    /**
     * Event name.
     *
     * @var string
     */
    private $name;

    /**
     * Event delegation status
     *
     * @var boolean
     */
    private $isStopped = false;

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->isStopped = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isStopped()
    {
        return $this->isStopped;
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
    public function __clone()
    {
        $this->isStopped = false;
    }
}
