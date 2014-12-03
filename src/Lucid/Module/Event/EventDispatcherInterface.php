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
 * @interface EventDispatcherInterface
 *
 * @package Lucid\Module\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface EventDispatcherInterface
{
    /**
     * Default priority level.
     * @var int
     */
    const PRIORITY_NORMAL = 0;

    /**
     * High priority level.
     * @var int
     */
    const PRIORITY_HIGH = 1000;

    /**
     * Low priority level.
     * @var int
     */
    const PRIORITY_LOW = -1000;

    /**
     * Register a handler for one or more events.
     *
     * @param string|array $events event names.
     * @param mixed        $handler the event handler
     * @param int          $priority dispatch priority
     *
     * @return void
     */
    public function addHandler($events, $handler, $priority = self::PRIORITY_NORMAL);

    /**
     * Remove one or all handers for given events
     *
     * @param string|array $events
     * @param mixed $handler
     *
     * @return void
     */
    public function removeHandler($events, $handler = null);

    /**
     * Register a subscriber.
     *
     * @param SubscriberInterface $subscriber
     *
     * @return void
     */
    public function addSubscriber(SubscriberInterface $subscriber);

    /**
     * Remove a subscriber.
     *
     * @param SubscriberInterface $subscriber
     *
     * @return void
     */
    public function removeSubscriber(SubscriberInterface $subscriber);

    /**
     * Check if a event has been registered.
     *
     * @param string $event
     *
     * @return boolean
     */
    public function hasEvent($event);

    /**
     * Dispatches an array of Event objects.
     *
     * @param array $events
     *
     * @return void
     */
    public function dispatchEvents(array $events);

    /**
     * Dispatches an event for a single Event object.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function dispatchEvent(EventInterface $event);

    /**
     * Dispatch one or multple events.
     *
     * @param string|array $eventName
     * @param EventInterface $event
     *
     * @return void
     */
    public function dispatch($eventName, EventInterface $event = null);

    /**
     * Get the handler for one or more events.
     *
     * @param string|array $event
     *
     * @return array
     */
    public function getHandlers($event = null);
}
