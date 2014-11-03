<?php

/**
 * This File is part of the Selene\Adapter\Kernel\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel\Tests;

use Mockery as m;

/**
 * @class StackedKernelTest
 * @package Selene\Adapter\Kernel\Tests
 * @version $Id$
 */
abstract class AbstractHttpStackTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    protected function mockMiddleware($prio)
    {
        $m = m::mock('Lucid\Adapter\HttpKernel\Middleware');
        $m->shouldReceive('getPriority')->andReturn($prio);

        return $m;
    }

    protected function mockRequest()
    {
        return m::mock('Symfony\Component\HttpFoundation\Request');
    }

    protected function mockKernel()
    {
        $kernel = m::mock('Symfony\Component\HttpKernel\HttpKernelInterface');

        return $kernel;
    }

    protected function mockTerminableKernel()
    {
        $kernel = m::mock('Lucid\Adapter\HttpKernel\Middleware, Symfony\Component\HttpKernel\TerminableInterface');

        return $kernel;
    }
}
