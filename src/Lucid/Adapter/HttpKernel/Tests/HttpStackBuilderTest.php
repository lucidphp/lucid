<?php

/*
 * This File is part of the Lucid\Adapter\HttpKernel\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel\Tests;

use Mockery as m;
use Lucid\Adapter\HttpKernel\HttpStackBuilder;

/**
 * @class StackBuilderTest
 *
 * @package Lucid\Adapter\HttpKernel\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HttpStackBuilderTest extends AbstractHttpStackTest
{

    /** @test */
    public function itShouldBuildAStackedKernel()
    {
        $builder = new HttpStackBuilder($kernel = $this->mockKernel());

        $builder->add($a = $this->mockMiddleware(100));
        $builder->add($b = $this->mockMiddleware(10));
        $builder->add($c = $this->mockMiddleware(20));

        $b->shouldReceive('setKernel')->with($kernel);
        $b->shouldReceive('getKernel')->andReturn($kernel);

        $c->shouldReceive('setKernel')->with($b);
        $c->shouldReceive('getKernel')->andReturn($b);

        $a->shouldReceive('setKernel')->with($c);
        $a->shouldReceive('getKernel')->andReturn($c);

        $stack = $builder->make();

        $this->assertInstanceof('Lucid\Adapter\HttpKernel\HttpStack', $stack);

        $called = false;

        $a->shouldReceive('handle')->andReturnUsing(function () use (&$called) {
            $called = true;
        });

        $stack->handle($this->mockRequest());

        $this->assertTrue($called, 'handle() should have been called on middlware $a.');
    }
}
