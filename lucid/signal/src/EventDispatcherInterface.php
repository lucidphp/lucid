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
 * @interface EventDispatcherInterface
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface EventDispatcherInterface
{
    /**
     * Register a handler for one or more events.
     *
     * @param mixed $events event names.
     * @param mixed        $handler the event handler
     * @param int          $priority dispatch priority
     *
     * @return void
     */
    public function addHandler($events, $handler, int $priority = PriorityInterface::PRIORITY_NORMAL) : void;

    /**
     * Remove one or all handers for given events
     *
     * @param mixed $events Event name as string or array of event names.
     * @param mixed $handler callable handler or handler as string declaration
     *
     * @return void
     */
    public function removeHandler($events, $handler = null) : void;

    /**
     * Register a subscriber.
     *
     * @param \Lucid\Signal\SubscriberInterface $subscriber
     *
     * @return void
     */
    public function addSubscriber(SubscriberInterface $subscriber) : void;

    /**
     * Remove a subscriber.
     *
     * @param SubscriberInterface $subscriber
     *
     * @return void
     */
    public function removeSubscriber(SubscriberInterface $subscriber) : void;

    /**
     * Check if a event has been registered.
     *
     * @param string $event
     *
     * @return bool
     */
    public function hasEvent($event) : bool;

    /**
     * Dispatches an array of Event objects.
     *
     * @param array $events
     *
     * @return void
     */
    public function dispatchEvents(array $events) : void;

    /**
     * Dispatches an event for a single Event object.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function dispatchEvent(EventInterface $event) : void;

    /**
     * Dispatch one or multple events.
     *
     * @param mixed $eventName
     * @param \Lucid\Signal\EventInterface $event
     *
     * @return void
     */
    public function dispatch($eventName, EventInterface $event = null) : void;

    /**
     * Get the handler for one or more events.
     *
     * @param string $event
     *
     * @return array
     */
    public function getHandlers($event = null) : array;
}
