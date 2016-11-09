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

use Iterator;

/**
 * @class AbstractCollection
 *
 * @package Lucid\Collection
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractCollection implements CollectionInterface
{
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
            yield $index => $item;
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
    public function head(int $max = null) : CollectionInterface
    {
        return $this->slice(0, min($max ?: 1, $this->count()));
    }

    /**
     * {@inheritdoc}
     */
    public function tail(int $max = null) : CollectionInterface
    {
        $max = $max ?: (count($this) - 1);
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
        return new static(...array_filter($this->getData(), $filter ?: null, $flag ?: 0));
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
    public function map(callable $map, $type = null) : CollectionInterface
    {
        return $type === self::MAP_USE_BOTH ?
            $this->mapKey($map) :
            new static(...array_map($map, $this->getData()));
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
     * Must return the initial data set.
     *
     * @return array
     */
    abstract protected function getData() : array;

    /**
     * Like map() but uses the collection's key as a second argument.
     *
     * @param callable $map
     *
     * @return CollectionInterface
     */
    private function mapKey(callable $map) : CollectionInterface
    {
        $ret = [];

        foreach ($this->getData() as $key => $value) {
            $ret[$key] = $map($value, $key);
        }

        return new static(...$ret);
    }

}
