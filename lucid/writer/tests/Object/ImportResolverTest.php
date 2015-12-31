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

use Lucid\Writer\Object\ImportResolver;

/**
 * @class ImportResolverTest
 * @package Lucid\Writer
 * @version $Id$
 */
class ImportResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $res = new ImportResolver();
        $res->add('\Foo\Bar');
        $res->add('\Faz\Bar');

        $this->assertSame('FazBar', $res->getAlias('Faz\Bar'));

        $res = new ImportResolver();
        $res->add('\Foo');
        $res->add('\Acme\Foo');

        $this->assertSame('Foo', $res->getAlias('Foo'));
        $this->assertSame('AcmeFoo', $res->getAlias('Acme\Foo'));

        $res = new ImportResolver();
        $res->add('\Acme\Foo');
        $res->add('\Foo');

        $this->assertSame('Foo', $res->getAlias('Acme\Foo'));
        $this->assertSame('FooAlias', $res->getAlias('Foo'));
    }

    /** @test */
    public function itShouldGetImportsAndAlias()
    {
        $res = new ImportResolver();

        $this->assertSame('Foo\Bar', $res->getAlias('Foo\Bar'));

        $res->add('\Foo\Bar');

        $this->assertSame('Foo\Bar', $res->getImport('Foo\Bar'));
        $this->assertSame('Bar', $res->getAlias('Foo\Bar'));

        $res->add('\Faz\Bar');

        $this->assertSame('Faz\Bar as FazBar', $res->getImport('Faz\Bar'));
        $this->assertSame('baz as bar', $res->getImport('baz as bar'));
    }

    /** @test */
    public function itShouldHandleAsStatements()
    {
        $res = new ImportResolver();

        $res->add('\Lunar\Foo as FooAlias');
        $res->add('\Acme\Foo');
        $res->add('\Foo');

        $this->assertSame('FooAlias', $res->getAlias('Lunar\Foo'));
        $this->assertSame('AcmeFoo', $res->getAlias('Acme\Foo'));
        $this->assertSame('FooAliasAlias', $res->getAlias('Foo'));

        $res = new ImportResolver();

        $res->add('\Acme\Foo');
        $res->add('\Lunar\Foo as FooAlias');
        $res->add('\Foo');

        $this->assertSame('Foo', $res->getAlias('Acme\Foo'));
        $this->assertSame('FooAlias', $res->getAlias('Lunar\Foo'));
        $this->assertSame('FooAliasAlias', $res->getAlias('Foo'));

        $res = new ImportResolver();

        $res->add('\Acme\Foo');
        $res->add('\Foo');
        $res->add('\Lunar\Foo as FooAlias');

        $this->assertSame('Foo', $res->getAlias('Acme\Foo'));
        $this->assertSame('FooAlias', $res->getAlias('Foo'));
        $this->assertSame('LunarFoo', $res->getAlias('Lunar\Foo'));
    }

    /** @test */
    public function itIsExpectedThatIt()
    {
        $res = new ImportResolver();

        $res->add('\Lunar\Foo as FooAlias');

        $this->assertSame('FooAlias', $res->getAlias('\Lunar\Foo as FooAlias'));
        $this->assertSame('Lunar\Foo as FooAlias', $res->getImport('\Lunar\Foo as FooAlias'));
        $this->assertSame('Lunar\Bar as BarAlias', $res->getImport('\Lunar\Bar as BarAlias'));
    }
}
