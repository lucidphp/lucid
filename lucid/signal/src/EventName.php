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
 * @class EventName
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventName
{
    /** @var string */
    private $name;

    /** @var EventInterface */
    private $event;

    /**
     * Construct.
     *
     * @param EventInterface $event
     */
    public function __construct(EventInterface $event, $name = null)
    {
        $this->name = $name;
        $this->event = $event;
    }

    /**
     * Get the event name.
     *
     * @return string
     */
    public function getName()
    {
        if (null !== ($name = $this->name)) {
            return $name;
        }

        return $this->name = $this->parseEventName();
    }

    /**
     * @see EventName#getName()
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Parses an event object into a readable event name.
     *
     * @return string
     */
    private function parseEventName()
    {
        $name = basename(strtr(get_class($this->event), ['\\' => '/']));

        return strtolower(preg_replace('#[A-Z]#', '.$0', lcfirst($name)));
    }
}
