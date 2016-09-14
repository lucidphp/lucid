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
 * @class EventInterface
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface EventInterface
{
    /**
     * Stop event delegation for this event.
     *
     * @return void
     */
    public function stop() /*: void */;

    /**
     * Check if event delegation is stopped for this event.
     *
     * @return boolean
     */
    public function isStopped() /*: bool */;

    /**
     * Set the name of the event.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name) /*: void */;

    /**
     * Get the event name
     *
     * @return EventName
     */
    public function getName() : EventName;

    /**
     * Get the event name
     *
     * @return EventName
     */
    public function getOriginalName() : EventName;
}
