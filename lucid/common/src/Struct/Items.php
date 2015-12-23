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
     * {@inheritdoc}
     */
    public function push($value)
    {
        $this->data[] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($index, $value)
    {
        array_splice($this->data, (int)$index, 0, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function pop($index = null)
    {
        return null === $index ? array_pop($this->data) : current(array_splice($this->data, $index, 1));
    }

    /**
     * {@inheritdoc}
     */
    public function remove($value)
    {
        if (false === ($index = array_search($value, $this->data, true))) {
            throw new InvalidArgumentException('index out of bounds');
        }

        $this->pop($index);
    }

    /**
     * {@inheritdoc}
     */
    public function countValue($value)
    {
        return count(array_filter($this->data, function ($item) use ($value) {
            return $value === $item;
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function sort()
    {
        sort($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function reverse()
    {
        $this->data = array_reverse($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function extend(ListInterface $list)
    {
        $args = $list->toArray();
        array_unshift($args, null);
        $args[0] = &$this->data;

        call_user_func_array('array_push', $args);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
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
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);

        return $this;
    }
}
