<?php

/**
 * This File is part of the Selene\Adapter\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel\Tests;

use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Lucid\Adapter\HttpKernel\HttpStack;

/**
 * @class StackTest extends StackedKernelTest
 * @see StackedKernelTest
 *
 * @package Selene\Adapter\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class HttpStackTest extends AbstractHttpStackTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Adapter\HttpKernel\HttpStack', new HttpStack($this->mockKernel()));
        $this->assertInstanceof('Lucid\Adapter\HttpKernel\HttpStack', new HttpStack($this->mockMiddleware(0)));
    }

    /** @test */
    public function itShouldCallHandleOnItsParentKenel()
    {
        $stack = new HttpStack($kernel = $this->mockMiddleware(0));

        $request = $this->mockRequest();;

        $kernel->shouldReceive('handle')
            ->with($request, HttpKernelInterface::MASTER_REQUEST, true)
            ->andReturn(new Response('success'));

        $response = $stack->handle($request);
        $this->assertSame('success', $response->getContent());
    }

    /** @test */
    public function itShouldCallTerminateOnItsParentKenel()
    {
        $stack = new HttpStack($kernel = $this->mockTerminableKernel());

        $request = $this->mockRequest();;

        $response = new Response('error');

        $kernel->shouldReceive('terminate')
            ->with($request, $resp = m::mock('Symfony\Component\HttpFoundation\Response'))
            ->andReturnUsing(function ($req, $res) use ($response) {
                $response->setContent('success');
            });

        $stack->terminate($request, $resp);
        $this->assertSame('success', $response->getContent());
    }
}
