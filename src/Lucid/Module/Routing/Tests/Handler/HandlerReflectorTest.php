<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests\Handler;

use Lucid\Module\Routing\Handler\HandlerReflector;

/**
 * @class HandlerReflectorTest
 *
 * @package Lucid\Module\Routing\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HandlerReflectorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeTypeFunction()
    {
        $r = new HandlerReflector('is_array');

        $this->assertTrue($r->isFunction());
    }

    /** @test */
    public function itShouldBeTypeClosure()
    {
        $r = new HandlerReflector(function () {});

        $this->assertTrue($r->isClosure());
    }

    /** @test */
    public function itShouldBeTypeMethod()
    {
        $r = new HandlerReflector([$this, 'testMethod']);

        $this->assertTrue($r->isMethod());
        $this->assertTrue($r->isInstanceMethod());
    }

    /** @test */
    public function itShouldBeTypeStaticMethod()
    {
        $r = new HandlerReflector(__CLASS__.'::staticTestMethod');

        $this->assertTrue($r->isMethod());
        $this->assertTrue($r->isStaticMethod());

        $r = new HandlerReflector([__CLASS__, 'staticTestMethod']);

        $this->assertTrue($r->isMethod());
        $this->assertTrue($r->isStaticMethod());
    }

    /** @test */
    public function itShouldBeInvokedObjectType()
    {
        $obj = $this->getMock('InvokedObjMock', ['__invoke']);

        $r = new HandlerReflector($obj);

        $this->assertTrue($r->isInvokedObject());
    }

    /** @test */
    public function itShouldBeInvokable()
    {
        $r = new HandlerReflector([$this, 'testMethod']);

        $this->assertTrue($r->invokeArgs([]));
    }


    /** @test */
    public function itShouldReturnRightReflector()
    {
        $r = new HandlerReflector([$this, 'testMethod']);

        $this->assertInstanceof('ReflectionMethod', $r->getReflector());

        $r = new HandlerReflector('array_map');

        $this->assertInstanceof('ReflectionFunction', $r->getReflector());
    }

    public function testMethod()
    {
        return true;
    }

    public static function staticTestMethod()
    {
    }
}
