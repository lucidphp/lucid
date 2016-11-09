<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Collection package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Collection\Tests;

use Lucid\Collection\AbstractCollection;
use Lucid\Collection\CollectionInterface;

/**
 * Class AbstractCollectionTest
 * @package Lucid\Collection
 * @author  Thomas Appel <mail@thomas-appel.com>
 */
class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**  @test */
    public function itShouldBeArrayable()
    {
        $collection = $this->newIntegers(1, 4, 12);
        $this->assertSame([1, 4, 12], $collection->toArray());
    }

    /** @test */
    public function itShouldMapData()
    {
        $collection = $this->newIntegers(1, 2, 3);
        $new = $collection->map(function (int $num) {
            return pow($num, 2);
        });

        $this->assertFalse($collection === $new);
        $this->assertSame([1, 4, 9], $new->toArray());
    }

    /** @test */
    public function itShouldBeCountable()
    {
        $collection = $this->newIntegers(1, 2, 3);
        $this->assertSame(3, count($collection));
    }

    /** @test */
    public function itShouldBeSliceable()
    {
        $collection = $this->newIntegers(1, 2, 3);
        $new = $collection->slice(0, 2);
        $this->assertSame([1, 2], $new->toArray());
    }

    /** @test */
    public function itShouldBeIteratable()
    {
        $collection = $this->newIntegers(1, 2, 3);
        $this->assertInstanceOf(\Traversable::class, $collection);

        $arr = [];
        foreach ($collection as $key => $value) {
            $arr[$key] = $value;
        }

        $this->assertSame($arr, $collection->toArray());
    }

    /** @test */
    public function itShouldGetTheTail()
    {
        $collection = $this->newIntegers(1, 2, 3, 4, 5);
        $new = $collection->tail();

        $this->assertFalse($collection === $new);
        $this->assertSame([2, 3, 4, 5], $new->toArray());

        $this->assertSame([5], $collection->tail(1)->toArray());
    }

    /** @test */
    public function itShouldGetTheHead()
    {
        $collection = $this->newIntegers(1, 2, 3, 4, 5);
        $new = $collection->head();

        $this->assertFalse($collection === $new);
        $this->assertSame([1], $new->toArray());

        $this->assertSame([1, 2], $collection->head(2)->toArray());
    }

    /** @test */
    public function itShouldIterateOverEachItem()
    {
        $items = [];
        $collection = $this->newIntegers(1, 2, 3);
        $collection->each(function (int $num) use (&$items) {
            $items[] = pow($num, 2);
        });

        $this->assertSame([1, 4, 9], $items);
    }

    /** @test */
    public function itShouldFilterValues()
    {
        $collection = $this->newIntegers(1, 4, 12);
        $new = $collection->filter(function (int $num) {
            return $num > 2;
        });

        $this->assertFalse($collection === $new);
        $this->assertSame([4, 12], $new->toArray());
    }

    /** @test */
    public function itShouldFilterValuesUsingKey()
    {
        $collection = $this->newIntegers(1, 4, 12);
        $new = $collection->filter(function (int $key) {
            return $key > 0;
        }, CollectionInterface::FILTER_USE_KEY);

        $this->assertSame([4, 12], $new->toArray());
    }

    /** @test */
    public function itShouldFilterValuesUsingKeyAndValue()
    {
        $collection = $this->newIntegers(1, 4, 12);
        $new = $collection->filter(function (int $num, int $key) {
            return $key > 0 && $num > 4;
        }, CollectionInterface::FILTER_USE_BOTH);

        $this->assertSame([12], $new->toArray());
    }

    /** @test */
    public function itShouldRejectValues()
    {
        $collection = $this->newIntegers(1, 4, 12);
        $new = $collection->reject(function (int $num) {
            return $num < 2;
        });

        $this->assertFalse($collection === $new);
        $this->assertSame([4, 12], $new->toArray());
    }

    /** @test */
    public function itShouldReduceItems()
    {
        $collection = $this->newIntegers(1, 2, 3);
        $this->assertSame(6, $collection->reduce(function (int $num = null, int $next) {
            return $num ? $num + $next : $next;
        }));
    }

    /**
     * Returns an concrete collection implementation.
     *
     * @param \int[] ...$args
     *
     * @return \Lucid\Collection\CollectionInterface
     */
    private function newIntegers(int ...$args) : CollectionInterface
    {
        return new class(...$args) extends AbstractCollection
        {
            /** @var int[]  */
            private $data;

            /**
             *  constructor.
             *
             * @param \int[] ...$args
             */
            public function __construct(int ...$args)
            {
                $this->data = $args;
            }

            /**
             * @return int[]
             */
            protected function getData() : array
            {
                return $this->data;
            }
        };
    }
}
