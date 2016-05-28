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
    const SORT_NATSORT = 1;

    const FILTER_USE_BOTH = ARRAY_FILTER_USE_BOTH;
    
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
     * filter
     *
     * @param callable $filter
     *
     * @return CollectionInterface
     */
    public function filter(callable $filter = null, int $flag = null) : self;

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
     * @param callable $map
     *
     * @return CollectionInterface
     */
    public function each(callable $map) : self;

    /**
     * reverse
     *
     * @return CollectionInterface
     */
    public function reverse() : self;

    /**
     * sort
     *
     * @param callable $sort
     *
     * @return void
     */
    //public function sort(callable $sort = null, int $sortn = self::SORT_NATSORT) : self;

    /**
     * walk
     *
     * @param callable $walk
     *
     * @return void
     */
    //public function walk(callable $walk) : self;

    /**
     * find
     *
     * @param mixed $item
     *
     * @return mixed
     */
    //public function find($item) : int;

    /**
     * reduce
     *
     * @param callable $reduce
     *
     * @return mixed
     */
    public function reduce(callable $reduce);
}
