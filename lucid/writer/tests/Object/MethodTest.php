<?php

/**
 * This File is part of the Lucid\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Tests\Object;

use Lucid\Writer\Writer;
use Lucid\Writer\Object\Method;
use Lucid\Writer\Object\Argument;

/**
 * @class MethodTest
 * @package Lucid\Writer
 * @version $Id$
 */
class MethodTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeStringifiable()
    {
        $method = new Method('setFoo');

        $expected = <<<PHP
    /**
     * setFoo
     *
     * @return void
     */
    public function setFoo()
    {
    }
PHP;
        $this->assertSame($expected, (string)$method);
    }

    /** @test */
    public function itShouldAddArguments()
    {
        $method = new Method('setFoo');

        $expected = <<<PHP
    /**
     * setFoo
     *
     * @param stdClass \$foo
     *
     * @return void
     */
    public function setFoo(stdClass \$foo)
    {
    }
PHP;

        $method->addArgument(new Argument('foo', 'stdClass'));
        $this->assertSame($expected, $method->generate());

        $method = new Method('setFoo');

        $method->setArguments([new Argument('foo', 'stdClass')]);
        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldAllowToAddComments()
    {
        $method = new Method('getFoo');

        $expected = <<<PHP
    /**
     * Foo
     * Bar
     *
     * @return void
     */
    public function getFoo()
    {
    }
PHP;

        $method->setDescription("Foo\nBar");

        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldNotAddReturnsOnConstructors()
    {
        $method = new Method('__construct');

        $expected = <<<PHP
    /**
     * Constructor.
     */
    public function __construct()
    {
    }
PHP;
        $method->setDescription('Constructor.');
        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itsTypeShouldBeSettable()
    {

        $method = new Method('getFoo');

        $expected = <<<PHP
    /**
     * getFoo
     *
     * @return string
     */
    public function getFoo()
    {
    }
PHP;
        $method->setType(Method::T_STRING);
        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldSetItsBody()
    {
        $method = new Method('getFoo');

        $expected = <<<PHP
    /**
     * getFoo
     *
     * @return void
     */
    public function getFoo()
    {
        return;
    }
PHP;
        $method->setBody('return;');
        $this->assertSame($expected, $method->generate());

        $method = new Method('getFoo');
        $method->setBody((new Writer)->writeln('return;'));
        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldAddAnnotations()
    {
        $expected = <<<PHP
    /**
     * This is doc a comment.
     *
     * This is the long description.
     *
     * @author Thomas Appel <mail@thomas-appel.com>
     * @param stdClass \$foo
     *
     * @return void
     */
    public function getFoo(stdClass \$foo)
    {
        return;
    }
PHP;
        $method = new Method('getFoo');
        $method->setBody('return;');
        $method->setArguments([new Argument('foo', 'stdClass')]);
        $method->addAnnotation('author', 'Thomas Appel <mail@thomas-appel.com>');
        $method->setDescription('This is doc a comment.');
        $method->setLongDescription('This is the long description.');

        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldWriteLongDesc()
    {
        $method = new Method('setFoo');
        $method->addParam('string', 'bar', 'nonesense');
        $method->addArgument(new Argument('foo', 'string'));

        $expected = <<<PHP
    /**
     * setFoo
     *
     * @param string \$bar nonesense
     * @param string \$foo
     *
     * @return void
     */
    public function setFoo(\$foo)
    {
    }
PHP;

        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldWriteAbstract()
    {

        $expected = <<<PHP
    /**
     * getFoo
     *
     * @return void
     */
    abstract public function getFoo();
PHP;
        $method = new Method('getFoo');
        $method->setAbstract(true);
        $this->assertSame($expected, $method->generate());
    }
}
