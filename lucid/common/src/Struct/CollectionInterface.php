<?php

/*
 * This File is part of the Lucid\Common package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Struct;

use Iterator;

/**
 * @interface CollectionInterface
 *
 * @package Lucid\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CollectionInterface extends \Countable, \IteratorAggregate
{
    /** @var int **/
    const SORT_NATSORT = 1;

    /** @var int **/
    const FILTER_USE_BOTH = ARRAY_FILTER_USE_BOTH;

    /** @var int **/
    const FILTER_USE_KEY = ARRAY_FILTER_USE_KEY;

    /**
     * Returns the collection as array.
     *
     * @return array
     */
    public function toArray() : array;

    /**
     * head
     *
     * @param int $max
     *
     * @return CollectionInterface
     */
    public function head(int $max = 1) : self;

    /**
     * tail
     *
     * @param int $max
     *
     * @return CollectionInterface
     */
    public function tail(int $max = 1) : self;

    /**
     * Uses a callback function to filter values.
     *
     * @param callable $filter : bool
     *
     * @return CollectionInterface
     */
    public function filter(callable $filter = null, int $flag = null) : self;

    /**
     * Uses a callback function to reject values.
     *
     * @param callable $reject : bool
     *
     * @return CollectionInterface
     */
    public function reject(callable $reject = null, int $flag = null) : self;

    /**
     * map
     *
     * @param callable $map
     *
     * @return CollectionInterface
     */
    public function map(callable $map) : self;

    /**
     * map
     *
     * @param callable $each
     *
     * @return CollectionInterface
     */
    public function each(callable $each) : self;

    /**
     * reverse
     *
     * @return CollectionInterface
     */
    public function reverse() : self;

    /**
     * reduce
     *
     * @param callable $reduce
     *
     * @return mixed
     */
    public function reduce(callable $reduce);
}
