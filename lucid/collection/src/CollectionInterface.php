<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Collection package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Collection;

/**
 * @interface CollectionInterface
 *
 * @package Lucid\Collection
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

    /** @var int */
    const MAP_USE_BOTH = 256;

    /**
     * Returns the collection as array.
     *
     * @return array
     */
    public function toArray() : array;

    /**
     * Returns a new Collection containing the first item(s) of the
     * original collection.
     *
     * @param int $max maximum size of the head.
     *
     * @return CollectionInterface
     */
    public function head(int $max = null) : self;

    /**
     * Returns a new collection containing the last item(s) of the
     * original collection.
     *
     * @param int $max maximum size of the tail.
     *
     * @return CollectionInterface
     */
    public function tail(int $max = null) : self;

    /**
     * Uses a callback function to filter values.
     *
     * @param callable $filter : bool
     * @param int $flag
     *
     * @return CollectionInterface
     */
    public function filter(callable $filter = null, int $flag = null) : self;

    /**
     * Reversed filter.
     *
     * Items that are evaluated as `true` within the filter expression
     * will be filtered out.
     *
     * @param callable $filter : bool
     * @param int $flag
     *
     * @return CollectionInterface
     */
    public function reject(callable $filter = null, int $flag = null) : self;

    /**
     * map
     *
     * @param callable $map
     *
     * @return CollectionInterface
     */
    public function map(callable $map) : self;

    /**
     * Iterates over each element in the collection.
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
