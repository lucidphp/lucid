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

use Countable;
use ArrayAccess;
use Serializable;
use ArrayIterator;
use IteratorAggregate;
use InvalidArgumentException;

/**
 * @class Items
 *
 * @package Lucid\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Items implements ListInterface, ArrayAccess, Countable, Serializable, IteratorAggregate
{
    /** @var array */
    private $data;

    /**
     * Constructor.
     *
     * @param mixed $args
     */
    public function __construct(...$args)
    {
        $this->data = $args;
    }

    /**
     * push
     *
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function append($value)
    {
        $this->data[] = $value;
    }

    /**
     * push
     *
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function add($value)
    {
        $this->data[] = $value;
    }

    /**
     * insert
     *
     * @param int $index
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function insert($index, $value)
    {
        array_splice($this->data, $index, 0, $value);
    }

    /**
     * pop
     *
     * @param int $index
     *
     * @access public
     * @return mixed
     */
    public function pop($index = null)
    {
        return null === $index ? array_pop($this->data) : current(array_splice($this->data, $index, 1));
    }

    /**
     * remove
     *
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function remove($value)
    {
        if (false === ($index = array_search($value, $this->data, true))) {
            throw new InvalidArgumentException('index out of bounds');
        }

        $this->pop($index);
    }

    /**
     * count
     *
     * @param mixed $value
     *
     * @access public
     * @return int
     */
    public function countValue($value)
    {
        return count(array_filter($this->data, function ($item) use ($value) {
            return $value === $item;
        }));
    }

    /**
     * count
     *
     * @access public
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * sort
     *
     * @access public
     * @return mixed
     */
    public function sort()
    {
        sort($this->data);
    }

    /**
     * reverse
     *
     * @access public
     * @return mixed
     */
    public function reverse()
    {
        $this->data = array_reverse($this->data);
    }

    /**
     * extend
     *
     * @param ListInterface $list
     *
     * @access public
     * @return mixed
     */
    public function extend(ListInterface $list)
    {
        $args = $list->toArray();
        array_unshift($args, null);
        $args[0] = &$this->data;

        call_user_func_array('array_push', $args);
    }

    /**
     * toArray
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * offsetSet
     *
     * @param int $offset
     * @param mixed $value
     *
     * @access public
     * @return voic
     */
    public function offsetSet($offset, $value)
    {
        $this->append($value);
    }

    /**
     * offsetGet
     *
     * @param int $offset
     *
     * @access public
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * offsetUnset
     *
     * @param int $offset
     *
     * @access public
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * offsetExists
     *
     * @param int $offset
     *
     * @access public
     * @return boolean
     */
    public function offsetExists($offset)
    {
        isset($this->data[$offset]);
    }

    /**
     * getIterator
     *
     * @access public
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * serialize
     *
     * @access public
     * @return string
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * unserialize
     *
     * @param mixed $data
     *
     * @access public
     * @return ListStruct
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
        return $this;
    }
}
