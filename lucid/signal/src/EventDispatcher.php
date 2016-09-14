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

use InvalidArgumentException;

/**
 * @class EventDispatcher
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventDispatcher implements EventDispatcherInterface
{
    /** @var array */
    private $handlers = [];

    /**
     * {@inheritdoc}
     */
    public function addHandler($events, $handler, int $priority = PriorityInterface::PRIORITY_NORMAL) : void
    {
        foreach ((array)$events as $event) {
            $this->getHandlerQueue($event)->add($this->getHandler($handler), (int)$priority);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeHandler($events, $handler = null) : void
    {
        foreach ((array)$events as $event) {
            if (null === $handler) {
                unset($this->handlers[$event]);
                continue;
            }

            if ($this->hasEvent($event)) {
                $this->getHandlerQueue($event)->remove($this->getHandler($handler));
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(SubscriberInterface $subscriber) : void
    {
        foreach ($subscriber->getSubscriptions() as $name => $methods) {
            if (is_array($methods) && 2 === count($methods) && is_int($methods[1])) {
                $this->addHandler($name, [$subscriber, $methods[0]], $methods[1]);

                continue;
            }

            foreach ((array)($methods) as $method) {
                list($method, $prio) = array_pad((array)$method, 2, 0);
                $this->addHandler($name, [$subscriber, $method], $prio);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(SubscriberInterface $subscriber) : void
    {
        foreach ($subscriber->getSubscriptions() as $name => $methods) {
            if (is_array($methods) && 2 === count($methods) && is_int($methods[1])) {
                $this->removeHandler($name, [$subscriber, $methods[0]]);

                continue;
            }

            foreach ((array)($methods) as $method) {
                list($method, ) = array_pad((array)$method, 2, 0);
                $this->removeHandler($name, [$subscriber, $method]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchEvents(array $events) : void
    {
        foreach ($events as $event) {
            $this->dispatchEvent($event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchEvent(EventInterface $event) : void
    {
        $this->dispatch((string)(new EventName($event)), $event);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, EventInterface $event = null) : void
    {
        $event = $event ?: new Event;

        if ($event instanceof ChainedEventInterface) {
            $event->setDispatcher($this);
        }

        foreach ((array)$eventName as $name) {
            if (!$this->hasEvent($name)) {
                continue;
            }

            $event->setName($name);

            foreach ($this->handlers[$name]->all() as $handler) {
                if ($event->isStopped()) {
                    break;
                }

                $handler($event);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlers($events = null) : array
    {
        $handlers = [];

        if (null === $events) {
            foreach ($this->handlers as $event => $queue) {
                $handlers = array_merge($handlers, $queue->toArray());
            }
        }

        foreach ((array)$events as $event) {
            if ($this->hasEvent($event)) {
                $handlers = array_merge($handlers, $this->handlers[$event]->toArray());
            }
        }

        return $handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasEvent($event) : bool
    {
        return array_key_exists($event, $this->handlers);
    }

    /**
     * getHandler
     *
     * @param  mixed $handler
     * @throws InvalidArgumentException
     *
     * @return callable
     */
    protected function getHandler($handler) : callable
    {
        if ($handler instanceof HandlerInterface) {
            return [$handler, 'handleEvent'];
        }

        if (is_callable($handler)) {
            return $handler;
        }

        throw new InvalidArgumentException(
            sprintf('Invalid handler "%s".', is_string($handler) ? $handler : gettype($handler))
        );
    }

    /**
     * getHandlerQueue
     *
     * @param mixed $event
     *
     * @return PriorityInterface
     */
    private function getHandlerQueue($event) : PriorityInterface
    {
        if (!isset($this->handlers[$event])) {
            $this->handlers[$event] = new Priority;
        }

        return $this->handlers[$event];
    }
}
