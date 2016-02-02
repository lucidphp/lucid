<?php

namespace Lucid\Mux\Tests\Matcher;

use Lucid\Mux\Matcher\Context;
use Lucid\Mux\Matcher\ContextInterface;
use Lucid\Mux\Matcher\RequestMatcherInterface as Matcher;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function contextWillReturnProperties()
    {
        $ctx = new Context(Matcher::MATCH, 'index', '/', 'action', $vars = ['id' => 12]);
        $this->assertTrue($ctx->isMatch());
        $this->assertEquals('/', $ctx->getPath());
        $this->assertEquals('index', $ctx->getName());
        $this->assertEquals('action', $ctx->getHandler());
        $this->assertSame($vars, $ctx->getVars());
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\Matcher\ContextInterface', new Context(Matcher::NOMATCH, null, null, null));
    }

    /** @test */
    public function itShouldBeMatchFailure()
    {
        $this->assertFalse((new Context(Matcher::NOMATCH, null, null, null))->isMatch());
    }

    /** @test */
    public function itShouldBeMethodFailure()
    {
        $this->assertTrue(($ctx = new Context(Matcher::NOMATCH_METHOD, null, null, null))->isMethodMissmatch());
    }

    /** @test */
    public function itShouldBeHostFailure()
    {
        $this->assertTrue(($ctx = new Context(Matcher::NOMATCH_HOST, null, null, null))->isHostMissmatch());
    }

    /** @test */
    public function itShouldBeSchemeFailure()
    {
        $this->assertTrue(($ctx = new Context(Matcher::NOMATCH_SCHEME, null, null, null))->isSchemeMissmatch());
    }
}
