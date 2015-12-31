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

use Lucid\Writer\Object\Method;
use Lucid\Writer\Object\Property;
use Lucid\Writer\Object\TraitWriter;

/**
 * @class ConstantTest
 * @package Lucid\Writer
 * @version $Id$
 */
class TraitWriterTest extends AbstractWriterTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Writer\Object\TraitWriter', $this->newObw());
    }

    /** @test */
    public function itShouldWriteSimpleTraits()
    {
        $tw = $this->newObw('FooTrait', 'Acme\Traits');
        $tw->noAutoGenerateTag();

        $tw->addMethod(new Method('setThing'));
        $this->assertSame($this->getContents('trait.0.php'), (string)$tw->generate());
    }

    /** @test */
    public function itShouldRejectInterfaceMethods()
    {
        $tw = $this->newObw('FooTrait', 'Acme\Traits');
        $m = $this->getMockbuilder('Lucid\Writer\Object\InterfaceMethod')
            ->disableOriginalConstructor()
            ->getMock();
        $m->method('getName')->willReturn('foo');

        try {
            $tw->addMethod($m);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('Trait method "foo" must not be instance of "InterfaceMethod".', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldCheckItemsBeforeMethods()
    {
    }

    /** @test */
    public function itShouldCompileToConstnatString()
    {
        $tw = $this->newObw('FooTrait', 'Acme\Traits');
        $tw->noAutoGenerateTag();

        $tw->addProperty(new Property('foo'));
        $tw->addTrait('Acme\Traits\BarTrait');
        $tw->addTrait('Acme\Test\HelperTrait');
        $tw->useTraitMethodAs('Acme\Traits\BarTrait', 'getFoo', 'bla');
        $tw->replaceTraitConflict('Acme\Traits\BarTrait', 'Acme\Test\HelperTrait', 'retrieve');

        $this->assertSame($this->getContents('trait.1.php'), (string)$tw->generate());
    }

    protected function newObw($name = 'MyObject', $namespace = null, $parent = null)
    {
        return new TraitWriter($name, $namespace);
    }
}
