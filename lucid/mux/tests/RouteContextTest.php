<?php

namespace Lucid\Mux\Tests;

use Lucid\Mux\RouteContext as Ctx;

class RouteContextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\RouteContextInterface', new Ctx('/foo', []));
    }

    /** @test */
    public function itShouldGetPath()
    {
        $ctx = new Ctx('/foo', '');
        $this->assertSame('/foo', $ctx->getStaticPath());
    }


    /** @test */
    public function itShouldExtractVarsFromTokens()
    {
        $ctx = new Ctx('', '', ['bar', 'baz']);

        $this->assertSame(['bar', 'baz'], $ctx->getVars());
    }
}
