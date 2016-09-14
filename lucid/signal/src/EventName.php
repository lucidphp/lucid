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
 * Class EventName
 * @package Lucid\Signal
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
     * @name string $name
     */
    public function __construct(EventInterface $event, string $name = null)
    {
        $this->event = $event;
        $this->name  = $name;
    }

    /**
     * Get the event name.
     *
     * @return string
     */
    public function getName() : string
    {
        if (!$this->isEmpty($this->name)) {
            return $this->name;
        }

        $name = $this->doGetName();

        return $this->name = $name ?: $this->parseEventName();
    }

    /**
     * @return null|string
     */
    private function doGetName() : ?string
    {
        foreach (['getName', 'getOriginalName'] as $fn) {
           if ($this !== ($name = $this->event->{$fn}())) {
               return $name;
           }
        }

        return null;
    }

    /**
     * @see EventName#getName()
     */
    public function __toString() : string
    {
        return $this->getName();
    }

    /**
     * Parses an event object into a readable event name.
     *
     * @return string
     */
    private function parseEventName() : string
    {
        $name = basename(strtr(get_class($this->event), ['\\' => '/']));

        return strtolower(preg_replace('#[A-Z]#', '.$0', lcfirst($name)));
    }

    /**
     * @param $name
     * @return bool
     */
    private function isEmpty($name) : bool
    {
        return null === $name || (is_string($name) && empty($name));
    }
}
