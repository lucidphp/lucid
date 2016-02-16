<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Tests\Object;

use Lucid\Writer\Object\Argument;

/**
 * @class ArgumentTest
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ArgumentTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGenerateArgs()
    {
        $arg = new Argument('foo', 'stdClass', 'null');

        $this->assertSame('stdClass $foo = null', $arg->generate());
    }

    /** @test */
    public function argTypesShouldBeSettable()
    {
        $arg = new Argument('foo');

        $arg->setType('stdClass');

        $this->assertSame('stdClass $foo', $arg->generate());
    }

    /** @test */
    public function defaultsShouldBeSettable()
    {
        $arg = new Argument('foo', 'stdClass');

        $arg->setDefault('null');

        $this->assertSame('stdClass $foo = null', $arg->generate());
    }

    /** @test */
    public function itShouldBeStringifiable()
    {
        $arg = new Argument('foo');
        $this->assertSame('$foo', (string)$arg);
    }

    /** @test */
    public function itShouldHandleVariadicArguments()
    {
        $arg = new Argument('args');
        $arg->isVariadic(true);

        $this->assertSame('...$args', (string)$arg);
    }

    /** @test */
    public function itShouldAcceptReferencedVariadics()
    {
        $arg = new Argument('args');
        $arg->isVariadic(true);
        $arg->isReference(true);

        $this->assertSame('&...$args', (string)$arg);
    }

    /** @test */
    public function itShouldBeReference()
    {
        $arg = new Argument('foo');
        $arg->isReference(true);
        $this->assertSame('&$foo', (string)$arg);
    }
}
