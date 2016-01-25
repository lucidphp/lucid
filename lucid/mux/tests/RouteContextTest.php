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
        $a = $this->mockToken('Lucid\Mux\Parser\Variable');
        $a->value = 'bar';
        $b = $this->mockToken();

        $ctx = new Ctx('', '', $tokens = [$b, $a]);

        $this->assertSame(['bar'], $ctx->getVars());
    }

    private function mockToken($class = 'Lucid\Mux\Parser\TokenInterface')
    {
        return $this->getMockbuilder($class)->disableOriginalConstructor()->getMock();
    }
}
