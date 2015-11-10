<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests\Cookie;

use Lucid\Http\Cookie\Cookie;
use Lucid\Http\Cookie\CookieJar;

/**
 * @class CookieJarTest
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CookieJarTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Http\Cookie\CookieJarInterface', new CookieJar);
    }

    /** @test */
    public function itShouldSetCookie()
    {
        $jar = new CookieJar;

        $c = $this->mockCookie('foo');
        $jar->set($c);

        $this->assertTrue($jar->has('foo'));
    }

    /** @test */
    public function itShouldGetCookie()
    {
        $jar = new CookieJar;

        $cA = $this->mockCookie('foo');
        $cB = $this->mockCookie('foo', '/', 'example.com');

        $jar->set($cA);
        $jar->set($cB);

        $this->assertSame($cA, $jar->get('foo'));
        $this->assertSame($cB, $jar->get('foo', '/', 'example.com'));
    }

    /** @test */
    public function getCookieShouldReturnNullIfMissing()
    {
        $jar = new CookieJar;
        $this->assertNull($jar->get('foo'));
    }

    /** @test */
    public function removeShouldReturnNullIfMissing()
    {
        $jar = new CookieJar;
        $this->assertFalse($jar->remove('foo'));
    }

    /** @test */
    public function itShouldRemoveCookies()
    {
        $jar = new CookieJar;
        $c = $this->mockCookie('foo');
        $jar->set($c);

        $this->assertTrue($jar->remove('foo'));
        $this->assertFalse($jar->has('foo'));
    }

    /** @test */
    public function itShouldRemoveCookiesByPath()
    {
        $jar = new CookieJar;
        $cA = $this->mockCookie('foo', '/');
        $cB = $this->mockCookie('foo', '/user');
        $jar->set($cA);
        $jar->set($cB);

        $this->assertTrue($jar->remove('foo', '/user'));
        $this->assertTrue($jar->has('foo'));
        $this->assertFalse($jar->has('foo', '/user'));
    }

    /** @test */
    public function itShouldRemoveCookiesByDomain()
    {
        $jar = new CookieJar;
        $c = $this->mockCookie('foo');
        $jar->set($c);
        $jar->removeByDomain(CookieJar::DOMAIN_DEFAULT);

        $this->assertFalse($jar->has('foo'));
    }

    /** @test */
    public function itShouldClearACookie()
    {
        $jar = new CookieJar;
        $c = $this->mockCookie('foo');
        $jar->clear('foo');

        $this->assertSame(1, $jar->get('foo')->getExpireTime());
    }

    /** @test */
    public function itShouldSetCookieCleared()
    {
        $jar = new CookieJar;
        $c = $this->mockCookie('foo');
        $jar->setCleared($c);

        $this->assertSame(1, $jar->get('foo')->getExpireTime());
    }

    /** @test */
    public function itShouldOutputFlatArray()
    {
        $jar = new CookieJar;
        $jar->set($this->mockCookie('foo'));
        $jar->set($this->mockCookie('bar'));

        $this->assertInternalType('array', $out = $jar->all());
        $this->assertArrayHasKey(0, $out);
        $this->assertArrayHasKey(1, $out);
    }

    /** @test */
    public function itShouldOutputNestedArray()
    {
        $jar = new CookieJar;
        $jar->set($this->mockCookie('foo'));
        $jar->set($this->mockCookie('bar', '/', 'example.com'));

        $this->assertInternalType('array', $out = $jar->all(CookieJar::OUTPUT_NESTED));
        $this->assertArrayHasKey(CookieJar::DOMAIN_DEFAULT, $out);
        $this->assertArrayHasKey('example.com', $out);
    }

    protected function mockCookie($name = 'cookie', $path = '/', $domain = null, $secure = false, $http = false)
    {
        $c = $this->getMock('Lucid\Http\Cookie\CookieInterface');
        $c->method('getName')->willReturn($name);
        $c->method('getPath')->willReturn($path ?: '/');
        $c->method('getDomain')->willReturn($domain);
        $c->method('isSecure')->willReturn($secure);
        $c->method('isHttpOnly')->willReturn($http);

        return $c;
    }
}
