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
 * @interface PriorityInterface
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface PriorityInterface
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
     * Adds a handler to the pool.
     *
     * @param mixed $handler
     * @param int $priority
     *
     * @return void
     */
    public function add($handler, $priority);

    /**
     * Removes a handler.
     *
     * @param mixed $handler
     *
     * @return bool
     */
    public function remove($handler);

    /**
     * Flushes handlers all handlers by priority
     * by returning an Iterator.
     *
     * @return \Iterator
     */
    public function flush();
}
