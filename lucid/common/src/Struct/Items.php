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
     * @param array $args
     */
    public function __construct(...$args)
    {
        $this->data = $args;
    }

    /**
     * {@inheritdoc}
     */
    public function push($value) : ListInterface
    {
        $this->data[] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(int $index, $value) : ListInterface
    {
        array_splice($this->data, $index, 0, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function pop(int $index = null)
    {
        return null === $index ? array_pop($this->data) : current(array_splice($this->data, $index, 1));
    }

    /**
     * {@inheritdoc}
     */
    public function remove($value) : ListInterface
    {
        if (false === ($index = array_search($value, $this->data, true))) {
            throw new InvalidArgumentException('index out of bounds');
        }

        $this->pop($index);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function countValue($value) : int
    {
        return count(array_filter($this->data, function ($item) use ($value) {
            return $value === $item;
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function count() : int
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function sort() : ListInterface
    {
        sort($this->data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reverse() : ListInterface
    {
        $this->data = array_reverse($this->data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function extend(ListInterface $list) : ListInterface
    {
        $args = $list->toArray();
        array_unshift($args, null);
        $args[0] = &$this->data;

        call_user_func_array('array_push', $args);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->push($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() : Iterator
    {
        return new ArrayIterator($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize() : string
    {
        return serialize($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data) : ListInterface
    {
        $this->data = unserialize($data);

        return $this;
    }
}
