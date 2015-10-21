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

        $expected = <<<EOL
    /**
     * setFoo
     *
     * @return void
     */
    public function setFoo()
    {
    }
EOL;
        $this->assertSame($expected, (string)$method);
    }

    /** @test */
    public function itShouldAddArguments()
    {
        $method = new Method('setFoo');

        $expected = <<<EOL
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
EOL;

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

        $expected = <<<EOL
    /**
     * Foo
     * Bar
     *
     * @return void
     */
    public function getFoo()
    {
    }
EOL;

        $method->setDescription("Foo\nBar");

        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldNotAddReturnsOnConstructors()
    {
        $method = new Method('__construct');

        $expected = <<<EOL
    /**
     * Constructor.
     */
    public function __construct()
    {
    }
EOL;
        $method->setDescription('Constructor.');
        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itsTypeShouldBeSettable()
    {

        $method = new Method('getFoo');

        $expected = <<<EOL
    /**
     * getFoo
     *
     * @return string
     */
    public function getFoo()
    {
    }
EOL;
        $method->setType(Method::T_STRING);
        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldSetItsBody()
    {
        $method = new Method('getFoo');

        $expected = <<<EOL
    /**
     * getFoo
     *
     * @return void
     */
    public function getFoo()
    {
        return;
    }
EOL;
        $method->setBody('return;');
        $this->assertSame($expected, $method->generate());

        $method = new Method('getFoo');
        $method->setBody((new Writer)->writeln('return;'));
        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldAddAnnotations()
    {
        $expected = <<<EOL
    /**
     * This is doc a comment.
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
EOL;
        $method = new Method('getFoo');
        $method->setBody('return;');
        $method->setArguments([new Argument('foo', 'stdClass')]);
        $method->addAnnotation('author', 'Thomas Appel <mail@thomas-appel.com>');
        $method->setDescription('This is doc a comment.');

        $this->assertSame($expected, $method->generate());
    }

    /** @test */
    public function itShouldWriteAbstract()
    {

        $expected = <<<EOL
    /**
     * getFoo
     *
     * @return void
     */
    abstract public function getFoo();
EOL;
        $method = new Method('getFoo');
        $method->setAbstract(true);
        $this->assertSame($expected, $method->generate());
    }
}
