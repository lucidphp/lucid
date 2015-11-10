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
     * @return void
     */
    public function append($value);

    /**
     * Insert a value at a given index.
     *
     * @param int $index the index
     * @param mixed $value the value to insert.
     *
     * @return void
     */
    public function insert($index, $value);

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
    public function pop($index = null);

    /**
     * Removes an item in the by its value.
     *
     * @param mixed $value the value to be removed.
     *
     * @return void
     */
    public function remove($value);

    /**
     * Gets the amount of values that you are looking for.
     *
     * @param mixed $value the value to search.
     *
     * @return int the amount
     */
    public function countValue($value);

    /**
     * sort
     *
     * @return mixed
     */
    public function sort();

    /**
     * reverse
     *
     * @return mixed
     */
    public function reverse();

    /**
     * extend
     *
     * @param ListStruct $list
     *
     * @return mixed
     */
    public function extend(ListInterface $list);
}
