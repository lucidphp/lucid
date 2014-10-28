<?php

/*
 * This File is part of the Lucid\Module\Event package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Event;

/**
 * @class Event
 *
 * @package Lucid\Module\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Event implements EventInterface
{
    private $name;
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

    public function __clone()
    {
        $this->isStopped = false;
    }
}
