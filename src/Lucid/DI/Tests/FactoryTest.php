<?php

/*
 * This File is part of the Lucid\DI\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests;

use Lucid\DI\Factory;
use Lucid\DI\ScopeInterface;

/**
 * @class FactoryTest
 *
 * @package Lucid\DI\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGetTheFactoryMethod()
    {
        $f = new Factory('someclass', 'someMethod');

        $this->assertSame('someMethod', $f->getFactoryMethod());
    }

    /** @test */
    public function itShouldBeStatic()
    {
        $f = new Factory('someclass', 'someMethod');

        $this->assertTrue($f->isStatic());
    }

    /** @test */
    public function itShouldBeNotBeStatic()
    {
        $f = new Factory('someclass', 'someMethod', false);

        $this->assertFalse($f->isStatic());
    }
}
