<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Tests\Handler;

use Lucid\Mux\Handler\Reflector;

/**
 * @class ReflectorTest
 *
 * @package Lucid\Mux\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ReflectorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeTypeFunction()
    {
        $r = new Reflector('is_array');

        $this->assertTrue($r->isFunction());
    }

    /** @test */
    public function itShouldBeTypeClosure()
    {
        $r = new Reflector(function () {
        });

        $this->assertTrue($r->isClosure());
    }

    /** @test */
    public function itShouldBeTypeMethod()
    {
        $r = new Reflector([$this, 'tMethod']);

        $this->assertTrue($r->isMethod());
        $this->assertTrue($r->isInstanceMethod());
    }

    /** @test */
    public function itShouldBeTypeStaticMethod()
    {
        $r = new Reflector(__CLASS__.'::staticTestMethod');

        $this->assertTrue($r->isMethod());
        $this->assertTrue($r->isStaticMethod());

        /** @var \ReflectionMethod $rf */
        $rf = $r->getReflector();
        $res = $rf->invokeArgs($this, $args = [1, 2]);
        $this->assertSame($res, $args);

        $r = new Reflector([__CLASS__, 'staticTestMethod']);

        $this->assertTrue($r->isMethod());
        $this->assertTrue($r->isStaticMethod());

        /** @var \ReflectionMethod $rf */
        $rf = $r->getReflector();
        $res = $rf->invokeArgs($this, $args = [3, 4]);
        $this->assertSame($res, $args);
    }

    /** @test */
    public function itShouldBeInvokedObjectType()
    {
        $obj = $this->getMockBuilder('InvokedObjMock')
            ->setMethods(['__invoke'])->getMock();
        $obj->method('__invoke')->willReturn('invoked');

        $r = new Reflector($obj);

        $this->assertTrue($r->isInvokedObject());

        $rf = $r->getReflector();
        $this->assertSame('invoked', $rf->invoke($obj));
    }

    /** @test */
    public function itShouldBeInvokable()
    {
        $r = new Reflector([$this, 'tMethod']);

        $this->assertTrue($r->invokeArgs([]));
    }


    /** @test */
    public function itShouldReturnRightReflector()
    {
        $r = new Reflector([$this, 'tMethod']);

        $this->assertInstanceof('ReflectionMethod', $r->getReflector());

        $r = new Reflector('array_map');

        $this->assertInstanceof('ReflectionFunction', $r->getReflector());
    }

    /** @skip */
    public function tMethod()
    {
        return true;
    }

    public static function staticTestMethod(...$args)
    {
        return $args;
    }
}
