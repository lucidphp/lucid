<?php

/*
 * This File is part of the Lucid\Http\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests;

use Lucid\Http\Request;

/**
 * @class RequestTest
 *
 * @package Lucid\Http\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Http\RequestInterface', new Request);
    }

    /** @test */
    public function itShouldGetProtocolVersion()
    {
        $request = new Request;
        $this->assertSame('1.1', $request->getProtocolVersion());

        $request = new Request([], [], [], ['SERVER_PROTOCOL' => 'http/1.0']);
        $this->assertSame('1.0', $request->getProtocolVersion());
    }

    /** @test */
    public function itShouldGetRequestMethod()
    {
        $request = new Request;
        $this->assertSame('GET', $request->getMethod());

        $request = new Request([], [], [], ['X_HTTP_METHOD_OVERRIDE' => 'PUT']);
        $this->assertSame('PUT', $request->getMethod());

        $request = new Request([], ['_method' => 'delete']);
        $this->assertSame('DELETE', $request->getMethod());
    }

    /** @test */
    public function itShouldCloneAllValueObjects()
    {
        $request = new Request([], [], [], [], [], [], 'dummy content');
        $body = $request->getBody();

        $clonedRequest = clone $request;

        foreach (['headers', 'files', 'cookies', 'attributes', 'server'] as $prop) {
            if ($request->{$prop} === $clonedRequest->{$prop}) {
                $this->fail(sprintf('Property %s was expected to be cloned.', $prop));
            }
        }

        $this->assertTrue($body !== $clonedRequest->getBody());
    }

    /** @test */
    public function itShouldCreateNewRequestWithProtocolVersion()
    {
        $request = new Request;

        $nR = $request->withProtocolVersion('1.0');

        $this->assertFalse($request === $nR);
        $this->assertSame('1.1', $request->getProtocolVersion());
        $this->assertSame('1.0', $nR->getProtocolVersion());
    }

    /** @test */
    public function itShouldCreateNewRequestWithStreamInterface()
    {
        $request = new Request;
        $nR = $request->withBody($body = $this->mockBody());

        $this->assertFalse($request === $nR);
        $this->assertSame($body, $nR->getBody());
    }

    protected function mockBody()
    {
        return $this->getMockBuilder('Psr\Http\Message\StreamInterface')->disableOriginalConstructor()->getMock();
    }
}
