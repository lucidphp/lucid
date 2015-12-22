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
     * Adds a thing to the pool.
     *
     * @param mixed $handler
     * @param int $priority
     *
     * @return void
     */
    public function add($thing, $priority);

    /**
     * Removes a thing.
     *
     * @param mixed $thing
     *
     * @return bool
     */
    public function remove($thing);

    /**
     * Returns all things ordered by priority
     * by returning an Iterator.
     *
     * @return \Iterator
     */
    public function all();

    /**
     * Like `all()`, but removes all things.
     *
     * @return void
     */
    public function flush();
}
