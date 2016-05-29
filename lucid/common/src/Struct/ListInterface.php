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

/**
 * @class ListInterface
 *
 * @package Lucid\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ListInterface
{
    /**
     * push
     *
     * @param mixed $value
     *
     * @return self
     */
    public function push($value);

    /**
     * Insert a value at a given index.
     *
     * @param int $index the index
     * @param mixed $value the value to insert.
     *
     * @return self
     */
    public function insert(int $index, $value) : self;

    /**
     * Removes a value from the end of the list
     *
     * If an index is given the value at the index is removed instead.
     *
     * @param int $index the index
     *
     * @return mixed the value specified by the index or the last one in the
     * list.
     */
    public function pop(int $index = null);

    /**
     * Removes an item in the by its value.
     *
     * @param mixed $value the value to be removed.
     *
     * @return self
     */
    public function remove($value) : self;

    /**
     * Gets the amount of values that you are looking for.
     *
     * @param mixed $value the value to search.
     *
     * @return int the amount
     */
    public function countValue($value) : int;

    /**
     * sort
     *
     * @return self
     */
    public function sort() : self;

    /**
     * reverse
     *
     * @return self
     */
    public function reverse() : self;

    /**
     * extend
     *
     * @param ListInterface $list
     *
     * @return self
     */
    public function extend(ListInterface $list) : self;
}
