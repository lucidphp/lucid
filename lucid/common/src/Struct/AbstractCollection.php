<?php

/*
 * This File is part of the Lucid\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Struct;

use Iterator;

/**
 * @class AbstractCollection
 *
 * @package Lucid\Common\Struct
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractCollection implements CollectionInterface
{
    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(...$data)
    {
        $this->getSetterMethod();
        call_user_func_array([$this, $this->getSetterMethod()], $data);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return $this->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() : Iterator
    {
        foreach ($this->getData() as $index => $item) {
            yield $item;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count() : int
    {
        return count($this->getData());
    }

    /**
     * {@inheritdoc}
     */
    public function head(int $max = 1) : CollectionInterface
    {
        return $this->slice(0, min($max, $this->count()));
    }

    /**
     * {@inheritdoc}
     */
    public function tail(int $max = 1) : CollectionInterface
    {
        return $this->slice(0 - min($max, $this->count()), $max);
    }

    /**
     * {@inheritdoc}
     */
    public function slice(int $offset = 1, int $count = 1) : CollectionInterface
    {
        return new static(...array_slice($data = $this->getData(), $offset, $count));
    }

    /**
     * {@inheritdoc}
     */
    public function reverse() : CollectionInterface
    {
        return new static(...array_reverse($this->getData()));
    }

    /**
     * {@inheritdoc}
     */
    public function filter(callable $filter = null, int $flag = null) : CollectionInterface
    {
        return new static(...array_filter($this->getData(), $filter ?: null, $flag));
    }

    /**
     * {@inheritdoc}
     */
    public function reject(callable $filter = null, int $flag = null) : CollectionInterface
    {
        return $this->filter(function (...$args) use ($filter) {
            return !$filter(...$args);
        }, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function map(callable $map) : CollectionInterface
    {
        return new static(...array_map($map, $this->getData()));
    }

    /**
     * {@inheritdoc}
     */
    public function each(callable $each) : CollectionInterface
    {
        foreach ($this->getData() as $key => $value) {
            $each($value, $key);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce(callable $reduce)
    {
        return array_reduce($this->getData(), $reduce);
    }

    /**
     * Sets the initial data set.
     *
     * Should advocate coherent data consistency and type checking
     *
     * @return string
     */
    abstract protected function getSetterMethod() : string;

    /**
     * Must return the initial data set.
     *
     * @return array
     */
    abstract protected function getData() : array;
}
