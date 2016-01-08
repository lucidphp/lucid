<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests\Definition;

use Lucid\DI\Scope;
use Lucid\DI\Definition\Service;

/**
 * @class ServiceTest
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldSetItsClass()
    {
        $s = new Service($class = 'ServiceClass');
        $this->assertSame($class, $s->getClass());

        $s->setClass($class = 'Foo\Bar\ServiceClass');
        $this->assertSame($class, $s->getClass());
    }

    /** @test */
    public function itShouldReplaceArguments()
    {
        $s = new Service;
        $s->setArguments([1, 2]);
        $s->setArgument(100, 1);

        $this->assertSame([1, 100], $s->getArguments());
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     */
    public function itShouldThrowIfArgExceedsMaxBound()
    {
        $s = new Service('', [1, 2]);

        $s->setArgument('3', 2);
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     */
    public function itShouldThrowIfArgDeceedsMinBound()
    {
        $s = new Service('', [1, 2]);

        $s->setArgument('3', -1);
    }

    /** @test */
    public function itShouldBeAttributable()
    {
        $s = new Service;
        $s->setAttribute('tagname', $tagVals = ['foo', 'bar']);

        $this->assertSame($tagVals, $s->getAttribute('tagname'));
    }

    /** @test */
    public function itShouldAddCallers()
    {
        $s = new Service;
        $s->calls($c1 = $this->mockCaller());
        $s->calls($c2 = $this->mockCaller());

        $this->assertSame([$c1, $c2], $s->getCallers());
    }

    /** @test */
    public function itShouldSetCallers()
    {
        $s = new Service;
        $c1 = $this->mockCaller();
        $c2 = $this->mockCaller();

        $s->setCallers($callers = [$c1, $c2]);

        $this->assertSame($callers, $s->getCallers());
    }

    /** @test */
    public function itShouldHaveOrHaveNotScope()
    {
        $s = new Service;
        $s->setScope(new Scope(Scope::SINGLETON));

        $this->assertTrue($s->hasScope(Scope::SINGLETON));
        $this->assertFalse($s->hasScope(Scope::PROTOTYPE));

        $s->setScope(new Scope(Scope::PROTOTYPE));

        $this->assertFalse($s->hasScope(Scope::SINGLETON));
        $this->assertTrue($s->hasScope(Scope::PROTOTYPE));
    }

    /** @test */
    public function itShouldTestIfItsInstantiable()
    {
        $s = new Service;
        $this->assertFalse($s->isBluePrint());

        $s->markAsBlueprint(true);
        $this->assertTrue($s->isBluePrint());

        $s->markAsBlueprint(false);
        $this->assertFalse($s->isBluePrint());
    }

    private function mockCaller()
    {
        return $this->getMockbuilder('Lucid\DI\Reference\CallerInterface')->disableOriginalConstructor()->getMock();
    }
}
