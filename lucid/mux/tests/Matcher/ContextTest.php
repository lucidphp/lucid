<?php declare(strict_types=1);

namespace Lucid\Mux\Tests\Matcher;

use Lucid\Mux\Matcher\Context;
use Lucid\Mux\Matcher\ContextInterface;
use Lucid\Mux\Matcher\RequestMatcherInterface as Matcher;
use Lucid\Mux\Request\ContextInterface as RequestContext;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            ContextInterface::class,
            new Context(Matcher::NOMATCH, null, $this->mockRequest(), null)
        );
    }

    /** @test */
    public function contextWillReturnProperties()
    {
        $r = $this->mockRequest();
        $r->method('getPath')->willReturn('/');
        $ctx = new Context(Matcher::MATCH, 'index', $r, 'action', $vars = ['id' => 12]);
        $this->assertTrue($ctx->isMatch());
        $this->assertEquals('/', $ctx->getPath());
        $this->assertEquals('index', $ctx->getName());
        $this->assertEquals('action', $ctx->getHandler());
        $this->assertSame($vars, $ctx->getVars());
        $this->assertTrue($r === $ctx->getRequest());
    }

    /** @test */
    public function itShouldBeMatchFailure()
    {
        $ctx = new Context(Matcher::NOMATCH, null, $this->mockRequest(), null);
        $this->assertFalse($ctx->isMatch());
    }

    /** @test */
    public function itShouldBeMethodFailure()
    {
        $ctx = new Context(Matcher::NOMATCH_METHOD, null, $this->mockRequest(), null);
        $this->assertTrue($ctx->isMethodMismatch());
    }

    /** @test */
    public function itShouldBeHostFailure()
    {
        $ctx = new Context(Matcher::NOMATCH_HOST, null, $this->mockRequest(), null);
        $this->assertTrue($ctx->isHostMismatch());
    }

    /** @test */
    public function itShouldBeSchemeFailure()
    {
        $ctx = new Context(Matcher::NOMATCH_SCHEME, null, $this->mockRequest(), null);
        $this->assertTrue($ctx->isSchemeMismatch());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestContext
     */
    private function mockRequest()
    {
        return $this->getMockbuilder(RequestContext::class)
            ->disableOriginalConstructor()->getMock();
    }
}
