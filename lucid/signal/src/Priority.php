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

use RuntimeException;

/**
 * @class Priority
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Priority implements PriorityInterface
{
    /** @var array */
    private $lut = [];

    /** @var array */
    private $handlers = [];

    /** @var bool */
    private $sorted = false;

    /**
     * {@inheritdoc}
     */
    public function add($handler, $priority)
    {
        $this->sorted = false;
        $this->handlers[$priority][] = $handler;
        $this->lut[$this->getHandlerString($handler)][] = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($handler)
    {
        $str = $this->getHandlerString($handler);

        if (!isset($this->lut[$str])) {
            return false;
        }

        // remove handler from lut and handlers array
        foreach ($this->lut[$str] as $i => $prio) {
            unset($this->handlers[$prio]);
            unset($this->lut[$str][$i]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $this->sort();

        foreach ($this->handlers as $handlers) {
            foreach ($handlers as $handler) {
                yield $handler;
            }
        }
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        $handlers = [];

        foreach ($this->all() as $handler) {
            $handlers[] = $handler;
        }

        return $handlers;
    }

    /**
     * getHandlerString
     *
     * @param mixed $handler
     * @throws RuntimeException
     *
     * @return string
     */
    private function getHandlerString($handler)
    {
        if (is_string($handler)) {
            return $handler;
        }

        if (is_object($handler) || $handler instanceof \Closure) {
            return spl_object_hash($handler);
        }

        if (is_callable($handler) && is_array($handler)) {
            return $this->getHandlerString($handler[0]) . '@' . $handler[1];
        }

        throw new RuntimeException('Can\'t convert handler to string.');
    }

    /**
     * sort
     *
     * @return void
     */
    private function sort()
    {
        if ($this->sorted) {
            return;
        }

        krsort($this->handlers);
    }
}
