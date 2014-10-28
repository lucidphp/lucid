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
 * @class EventName
 *
 * @package Selene\Module\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class EventName
{
    /**
     * event
     *
     * @var EventInterface
     */
    private $event;

    /**
     * Construct.
     *
     * @param EventInterface $event
     */
    public function __construct(EventInterface $event)
    {
        $this->event = $event;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        if (null !== ($name = $this->event->getName())) {
            return $name;
        }

        return $this->parseEventName();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * getBaseEventName
     *
     * @return string
     */
    private function parseEventName()
    {
        $name = basename(strtr(get_class($this->event), ['\\' => '/']));

        return strtolower(preg_replace('#[A-Z]#', '.$0', lcfirst($name)));
    }
}
