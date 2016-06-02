<?php
/**
 * This File is part of the lucid package
 *
 *  (c) malcolm <$email>
 *
 *  For full copyright and license information, please refer to the LICENSE file
 *  that was distributed with this package.
 */

/**
 * Created by PhpStorm.
 * User: malcolm
 * Date: 27.05.16
 * Time: 19:32
 */

namespace Lucid\Common\Tests\Struct;

use Lucid\Common\Struct\AbstractCollection;
use Lucid\Common\Struct\CollectionInterface;
use Lucid\Common\Tests\Struct\Stubs\IntegerCollection;

class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(CollectionInterface::class, $this->newCollection());
    }

    /** @test */
    public function itShouldCallSetter()
    {
        $args = [1, 2, 3];

        $collection = $this->newCollection($args, $fn = function (int ...$args) use (&$collection) {
            $this->data = $args;
        });
    }

    /** @test */
    public function itShouldBeIteratable()
    {
        $data = [1, 2, 3];
        $collection = new IntegerCollection(...$data);

        $res = [];

        foreach ($collection as $index => $item) {
            $res[$index] = $item;
        }

        $this->assertEquals($data, $res);
    }

    /** @test */
    public function itShouldMapValues()
    {
        $data = [1, 2, 3];
        $collection = new IntegerCollection(...$data);

        $new = $collection->map(function (int $int) {
            return $int + 1;
        });

        $this->assertFalse($new === $collection);

        $result = $new->toArray();
        $this->assertSame([2, 3, 4], $result);
    }

    /** @test */
    public function itShouldReverseValues()
    {
        $data = [1, 2, 3];
        $collection = new IntegerCollection(...$data);

        $new = $collection->reverse();

        $this->assertFalse($new === $collection);

        $result = $new->toArray();
        $this->assertSame([3, 2, 1], $result);
    }

    /** @test */
    public function itShouldFilterValues()
    {
        $data = [1, 2, 3, 4, 5];
        $collection = new IntegerCollection(...$data);

        $new = $collection->filter([$this, 'filterAndReject']);

        $this->assertFalse($new === $collection);

        $result = $new->toArray();
        $this->assertSame([3, 4, 5], $result);
    }

    /** @test */
    public function itShouldRejectValues()
    {
        $data = [1, 2, 3, 4, 5];
        $collection = new IntegerCollection(...$data);

        $new = $collection->reject([$this, 'filterAndReject']);

        $this->assertFalse($new === $collection);

        $result = $new->toArray();
        $this->assertSame([1, 2], $result);
    }

    /**
     * Test helper for filter and reject
     *
     * @param int $int
     * @return bool
     */
    public function filterAndReject(int $int) : bool
    {
        return $int > 2;
    }

    /** @test */
    public function itShouldApplyFilterFlags()
    {
        $data = [1, 2, 3, 4, 5];
        $collection = new IntegerCollection(...$data);

        $new = $collection->filter(function (int $int, int $key) {
            return $key > 2;
        }, CollectionInterface::FILTER_USE_BOTH);

        $result = $new->toArray();
        $this->assertSame([4, 5], $result);
    }

    /** @test */
    public function itShouldRediceData()
    {
        $data = [1, 2, 3, 4, 5];
        $collection = new IntegerCollection(...$data);

        $res = $collection->reduce(function (int $prev = null, int $current = null) {
            if (null === $prev) {
                return $current;
            }

            return $current + $prev;
        });

        $this->assertSame(15, $res);
    }

    /** @test */
    public function itShouldReturnHead()
    {
        $data = [1, 2, 3, 4, 5];
        $collection = new IntegerCollection(...$data);

        $new = $collection->head();

        $result = $new->toArray();
        $this->assertSame([1], $result);

        $collection = new IntegerCollection(...$data);

        $new = $collection->head(3);

        $result = $new->toArray();
        $this->assertSame([1, 2, 3], $result);
    }

    /** @test */
    public function itShouldReturnTail()
    {
        $data = [1, 2, 3, 4, 5];
        $collection = new IntegerCollection(...$data);

        $new = $collection->tail();
        $result = $new->toArray();
        $this->assertSame([5], $result);

        $collection = new IntegerCollection(...$data);

        $new = $collection->tail(2);
        $result = $new->toArray();
        $this->assertSame([4, 5], $result);
    }

    /** @test */
    public function itShouldSlice()
    {
        $data = [1, 2, 3, 4, 5];
        $collection = new IntegerCollection(...$data);

        $new = $collection->slice(1, 2);
        $result = $new->toArray();
        $this->assertSame([2, 3], $result);

        $new = $collection->slice(-2, 2);
        $result = $new->toArray();
        $this->assertSame([4, 5], $result);
    }

    /** @test */
    public function itShouldBeCountable()
    {
        $data = [1, 2, 3, 4, 5];

        $collection = $this->newCollection($data);
        $this->assertSame(count($data), count($collection));
    }

    /** @test */
    public function itIterateWithEach()
    {
        $args = [1, 2, 3];

        $collection = $this->newCollection($args);

        $test = [];
        $collection->each(function (int $item, int $index) use (&$test) {
            $test[$index] = $item;
        });

        $this->assertSame($args, $test);
    }

    protected function newCollection(array $data = [], \Closure $setter = null, $stubClass = false)
    {
        if (false !== $stubClass) {
            return new $stubClass(...$data);
        }

        $collection = $this->getMockbuilder(AbstractCollection::class)
            ->disableOriginalConstructor()
            //->setConstructorArgs($data)
            ->setMethods(['getSetterMethod', 'getData', 'setData'])
            ->getMock();

        $collection->data = [];
        $collection->method('getSetterMethod')->willReturnCallback(function () {
            return 'setData';
        });

        $setter = $setter ?: function (...$data) use ($collection) {
            $collection->data = $data;
        };

        $setter->bindTo($collection);

        $collection->method('getData')->willReturnCallback(function () use ($collection) {
            return $collection->data;
        });

        $collection->method('setData')->willReturnCallback($setter ?: function (...$data) use ($collection) {
            $collection->data = $data;
        });

        $reflection = new \ReflectionClass(AbstractCollection::class);
        $constructor = $reflection->getConstructor();

        $constructor->invokeArgs($collection, $data);

        return $collection;
    }
}
