<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Mux\Tests\Request;

use Lucid\Mux\Request\Context;
use Zend\Diactoros\ServerRequestFactory;
use Lucid\Mux\Tests\Request\Fixures\Server;

/**
 * @class ContextTest
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\Request\ContextInterface', $this->newContext());
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $req = $this->mockZendRequest(['REQUEST_URI' => '/foo/bar?baz=bar', 'HTTP_HOST' => 'example.com']);

        $ctx = Context::fromPsrRequest($req);

        $this->assertSame('/foo/bar', $ctx->getPath());
        $this->assertSame('example.com', $ctx->getHost());
        $this->assertSame(80, $ctx->getHttpPort());
        $this->assertSame('http', $ctx->getScheme());

    }

    private function newContext()
    {
        return new Context;
    }

    private function mockZendRequest(array $server = [], array $get = [], array $request = [])
    {
        return ServerRequestFactory::fromGlobals(Server::mock($server));
    }

    private function mockPsrRequest()
    {
        $rq = $this->getMockbuilder('Psr\Http\Message\ServerRequestInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $rq->method('getUri')->willReturn($this->mockPsrUri());

        return $rq;
    }

    private function mockPsrUri()
    {
        return $this->getMockbuilder('Psr\Http\Message\UriInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
