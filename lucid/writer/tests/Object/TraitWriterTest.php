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
    public function itShouldCompileToConstnatString()
    {
        $tw = $this->newObw('FooTrait', 'Acme\Traits');

        $tw->addProperty(new Property('foo'));
        $tw->addTrait('Acme\Traits\BarTrait');
        $tw->addTrait('Acme\Test\HelperTrait');
        $tw->useTraitMethodAs('Acme\Traits\BarTrait', 'getFoo', 'bla');
        $tw->replaceTraitConflict('Acme\Traits\BarTrait', 'Acme\Test\HelperTrait', 'retrieve');
    }

    protected function newObw($name = 'MyObject', $namespace = null)
    {
        return new TraitWriter($name, $namespace);
    }
}
