<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests\Http;

use Lucid\Module\Routing\Http\RequestContext;

/**
 * @class RequestContextTest
 *
 * @package Lucid\Module\Routing\Tests\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequestContextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGetPath()
    {
        $r = new RequestContext('', '/foo');

        $this->assertSame('/foo', $r->getPath());
    }

    /** @test */
    public function itShouldGetBase()
    {
        $r = new RequestContext('base');

        $this->assertSame('base', $r->getBaseUrl());
    }

    /** @test */
    public function itShouldDefaultToGet()
    {
        $r = new RequestContext;

        $this->assertSame('GET', $r->getMethod());
    }

    /** @test */
    public function itShouldGetMethod()
    {
        $r = new RequestContext('', '/', 'POST');

        $this->assertSame('POST', $r->getMethod());
    }

    /** @test */
    public function itShouldGetQuery()
    {
        $r = new RequestContext('', '/');

        $this->assertSame('', $r->getQueryString());
    }

    /** @test */
    public function itShouldGetHost()
    {
        $r = new RequestContext('', '/');

        $this->assertSame('localhost', $r->getHost());
    }

    /** @test */
    public function itShouldGetPort()
    {
        $r = new RequestContext('', '/');

        $this->assertSame(80, $r->getHttpPort());

        $r = new RequestContext('', '/', 'GET', '', 'localhost', 'https', 443);

        $this->assertSame(443, $r->getHttpPort());
    }

    /** @test */
    public function itShouldGetScheme()
    {
        $r = new RequestContext('', '/', 'GET', '', 'localhost', 'https');
        $this->assertSame('https', $r->getScheme());
    }
}
