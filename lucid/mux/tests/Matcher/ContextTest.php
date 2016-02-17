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
        $ctx = new Context(Matcher::NOMATCH, null, null, null);
        $this->assertFalse($ctx->isMatch());
    }

    /** @test */
    public function itShouldBeMethodFailure()
    {
        $ctx = new Context(Matcher::NOMATCH_METHOD, null, null, null);
        $this->assertTrue($ctx->isMethodMissmatch());
    }

    /** @test */
    public function itShouldBeHostFailure()
    {
        $ctx = new Context(Matcher::NOMATCH_HOST, null, null, null);
        $this->assertTrue($ctx->isHostMissmatch());
    }

    /** @test */
    public function itShouldBeSchemeFailure()
    {
        $ctx = new Context(Matcher::NOMATCH_SCHEME, null, null, null);
        $this->assertTrue($ctx->isSchemeMissmatch());
    }
}
