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
 * @class EventDispatcher
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * Sorted events.
     *
     * string => bool
     * @var array
     */
    private $sorted = [];

    /**
     * Event Handlers
     *
     * string => [int => string]
     * @var array
     */
    private $handlers = [];

    /**
     * {@inheritdoc}
     */
    public function addHandler($events, $handler, $priority = self::PRIORITY_NORMAL)
    {
        list ($handler, $hash) = $this->getHandlerAndHash($handler);

        foreach ((array)$events as $event) {
            unset($this->sorted[$event]);
            $this->handlers[$event][$priority][$hash] = $handler;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeHandler($events, $handler = null)
    {
        $events = (array)$events;

        if (null !== $handler) {
            list ($handler, $hash) = $this->getHandlerAndHash($handler);
        } else {
            $hash = null;
        }

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
     * {@inheritdoc}
     */
    public function dispatchEvents(array $events)
    {
        foreach ($events as $event) {
            $this->dispatchEvent($event);
        }
    }

    /**
     * {@inheritdoc}
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

            $this->sort($name, $this->handlers);
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
    public function hasEvent($event)
    {
        return array_key_exists($event, $this->handlers);
    }

    /**
     * pullHandlers
     *
     * @param array $handlers
     *
     * @return array
     */
    private function pullHandlers(array $handlers)
    {
        $out = [];

        foreach ($handlers as $event => &$pri) {
            $this->sort($event, $handlers);
            foreach ($pri as $i => $handler) {
                foreach ($handler as $hash => $callable) {
                    $out[$hash] = $callable;
                }
            }
        }

        return array_values($out);
    }

    /**
     * sort
     *
     * @param mixed $event
     * @param array $handlers
     *
     * @return void
     */
    private function sort($event, array &$handlers)
    {
        if (!isset($this->sorted[$event])) {
            krsort($handlers[$event]);
            $this->sorted[$event] = true;
        }
    }

    /**
     * getHandlerAndHash
     *
     * @param mixed $handler
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    private function getHandlerAndHash($handler)
    {
        if ($handler instanceof HandlerInterface) {
            $handler = [$handler, 'handleEvent'];
        }

        if (is_callable($handler)) {
            if (is_string($handler)) {
                return [$handler, $handler];
            }

            if (is_array($handler)) {
                return [$handler, spl_object_hash($handler[0]).'@'.$handler[1], $handler];
            }

            return [$handler, spl_object_hash($handler)];
        }

        throw new \InvalidArgumentException(
            sprintf('Invalid handler "%s".', is_string($handler) ? $handler : gettype($handler))
        );
    }
}
