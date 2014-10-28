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
 * @class EventDispatcher
 *
 * @package Lucid\Module\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventDispatcher implements EventDispatcherInterface
{
    protected $sorted = [];
    protected $handlers = [];

    public function addHandler($events, $handler, $priority = 0)
    {
        $hash = $this->getHandlerHash($handler);

        foreach ((array)$events as $event) {
            $this->sorted[$event] = false;
            $this->handlers[$event][$priority][$hash] = $handler;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeHandler($events, $handler = null)
    {
        $events = (array)$events;

        $hash = $handler ? $this->getHandlerHash($handler) : null;

        foreach ((array)$events as $event) {
            if (!$this->hasEvent($event)) {
                continue;
            }

            if (null === $hash) {
                unset($this->handlers[$event]);
                continue;
            }

            foreach ($this->handlers[$event] as &$prio) {
                if (isset($prio[$hash])) {
                    unset($prio[$hash]);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscriptions() as $name => $methods) {

            if (is_array($methods) && 2 === count($methods) && is_int($methods[1])) {
                $this->addHandler($name, [$subscriber, $methods[0]], $methods[1]);

                continue;
            }

            foreach ((array)($methods) as $method) {
                list ($method, $prio) = array_pad((array)$method, 2, 0);
                $this->addHandler($name, [$subscriber, $method], $prio);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscriptions() as $name => $methods) {

            if (is_array($methods) && 2 === count($methods) && is_int($methods[1])) {
                $this->removeHandler($name, [$subscriber, $methods[0]]);

                continue;
            }

            foreach ((array)($methods) as $method) {
                list ($method,) = array_pad((array)$method, 2, 0);
                $this->removeHandler($name, [$subscriber, $method]);
            }
        }
    }

    /**
     * dispatchEvents
     *
     * @param array $events
     *
     * @return void
     */
    public function dispatchEvents(array $events)
    {
        foreach ($events as $event) {
            $this->dispatchEvent($event);
        }
    }

    /**
     * dispatchEvent
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function dispatchEvent(EventInterface $event)
    {
        $this->dispatch((string)(new EventName($event)), $event);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, EventInterface $event = null)
    {
        $event = $event ?: new Event;

        if ($event instanceof ChainedEventInterface) {
            $event->setDispatcher($this);
        }

        foreach ((array)$eventName as $name) {

            if (!$this->hasEvent($name)) {
                continue;
            }

            $this->sort($name);
            $event->setName($name);

            foreach ($this->handlers[$name] as &$handlers) {
                foreach ($handlers as $hash => &$handler) {
                    if ($event->isStopped()) {
                        break;
                    }

                    call_user_func($handler, $event);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlers($events = null)
    {
        if (null === $events) {
            return $this->pullHandlers($this->handlers);
        }

        return $this->pullHandlers(array_intersect_key($this->handlers, array_flip(((array)$events))));
    }

    /**
     * {@inheritdoc}
     */
    protected function pullHandlers(array $handlers)
    {
        $out = [];

        foreach ($handlers as $event => $pri) {
            foreach ($pri as $i => $handler) {
                foreach ($handler as $hash => $callable) {
                    $out[$hash] = $callable;
                }
            }
        }

        return array_values($out);
    }

    /**
     * hasEvent
     *
     * @param mixed $event
     *
     * @return boolean
     */
    public function hasEvent($event)
    {
        return array_key_exists($event, $this->handlers);
    }

    /**
     * sort
     *
     * @param mixed $event
     *
     * @return void
     */
    protected function sort($event)
    {
        if (isset($this->sorted[$event]) && false !== $this->sorted[$event]) {
            return;
        }

        krsort($this->handlers[$event]);
        $this->sorted[$event] = true;
    }

    /**
     * getHandlerHash
     *
     * @param mixed $handler
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getHandlerHash($handler)
    {
        if ($handler instanceof EventHandlerInterface) {
            $handler = [$handler, 'handleEvent'];
        }

        if (is_callable($handler)) {

            if (is_string($handler)) {
                return $handler;
            }

            if (is_array($handler)) {
                return spl_object_hash($handler[0]).'@'.$handler[1];
            }

            return spl_object_hash($handler);
        }

        throw new \InvalidArgumentException(
            sprintf('Invalid handler "%s".', is_string($handler) ? $handler : gettype($handler))
        );
    }
}
