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

use Lucid\Writer\Object\Constant;
use Lucid\Writer\Object\InterfaceWriter;
use Lucid\Writer\Object\InterfaceMethod;

/**
 * @class InterfaceWriterTest
 * @see AbstractWriterTest
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class InterfaceWriterTest extends AbstractWriterTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Writer\Object\AbstractWriter', new InterfaceWriter('MyObject'));
    }

    /** @test */
    public function itShouldDisallowClassMethods()
    {
        $cwr = $this->newObw('Acme\FooInterface');
        $m = $this->getMockbuilder('Lucid\Writer\Object\MethodInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $m->method('getName')->willReturn('foo');
        try {
            $cwr->addMethod($m);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('Method "foo" must be instance of "InterfaceMethod".', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldNotSetParent()
    {
        $cwr = $this->newObw('FooInterface', 'Acme', 'BarInterface');
        try {
            $cwr->setParent('Lube');
        } catch (\BadMethodCallException $e) {
            $this->assertEquals('Cannot set parent Parent. already set.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldBeExtendable()
    {
        $cwr = $this->newObw('Acme\FooInterface');
        $cwr->noAutoGenerateTag();
        $cwr->setParent('\Acme\BarInterface');

        $this->assertEquals($this->getContents('interface.0.php'), $cwr->generate());
    }

    /** @test */
    public function itShouldHaveConstants()
    {
        $cwr = $this->newObw('Acme\FooInterface');
        $cwr->noAutoGenerateTag();

        $cwr->setConstants([
            new Constant('t_foo', '12', 'int'),
            new Constant('t_bar', '13', 'string')
        ]);

        $this->assertEquals($this->getContents('interface.4.php'), $cwr->generate());
    }

    /** @test */
    public function itShouldHaveConstantsAndMethods()
    {
        $cwr = $this->newObw('Acme\FooInterface');
        $cwr->noAutoGenerateTag();

        $cwr->setConstants([
            new Constant('t_foo', '12', 'int')
        ]);

        $cwr->addMethod(new InterfaceMethod('setFoo'));
        $cwr->addMethod(new InterfaceMethod('setBar'));

        $this->assertEquals($this->getContents('interface.4.1.php'), $res = $cwr->generate());
    }

    /** @test */
    public function itShouldWriteNewLineBetweenConstantAndMethod()
    {
        $expected = <<<PHP
<?php

namespace Acme;

/**
 * @interface Foo
 */
interface Foo
{
    /** @var int */
    const MY_CONST = 1;

    /**
     * getMyConst
     *
     * @return int
     */
    public function getMyConst();
}

PHP;

        $cwr = $this->newObw('Acme\Foo');
        $cwr->noAutoGenerateTag();
        $cwr->addConstant(new Constant('MY_CONST', 1, 'int'));
        $cwr->addMethod(new InterfaceMethod('getMyConst', 'int'));

        $this->assertSame($expected, (string)$cwr->generate());
    }

    protected function newObw($name = 'MyObject', $namespace = null, $parent = null)
    {
        return new InterfaceWriter($name, $namespace, $parent);
    }
}
