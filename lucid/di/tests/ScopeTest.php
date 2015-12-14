<?php

namespace Lucid\DI\Tests;

use Lucid\DI\Scope;

class ScopeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\DI\Scope', new Scope);
    }

    /** @test */
    public function itShouldDefaultToSingleton()
    {
        $sc = new Scope;

        $this->assertSame(Scope::SINGLETON, (string)$sc);
    }

    /** @test */
    public function itShouldGetParentScope()
    {
        $sc = new Scope('bar', $parent = new Scope('foo'));

        $this->assertEquals($parent, $sc->getParent());
    }
}
