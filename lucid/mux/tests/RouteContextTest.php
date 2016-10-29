<?php declare(strict_types=1);

namespace Lucid\Mux\Tests;

use Lucid\Mux\RouteContext as Ctx;
use Lucid\Mux\RouteContextInterface;
use Lucid\Mux\Parser\VariableInterface;

class RouteContextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(RouteContextInterface::class, new Ctx('/foo', '.*'));
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
        $a = $this->mockToken(VariableInterface::class);
        $a->method('value')->willReturn('bar');
        $b = $this->mockToken();

        $ctx = new Ctx('', '', $tokens = [$b, $a]);

        $this->assertSame(['bar'], $ctx->getVars());
    }

    private function mockToken($class = 'Lucid\Mux\Parser\TokenInterface')
    {
        return $this->getMockbuilder($class)->disableOriginalConstructor()->getMock();
    }
}
