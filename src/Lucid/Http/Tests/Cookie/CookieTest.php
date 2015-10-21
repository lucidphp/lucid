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

/**
 * @class CookieTest
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CookieTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGetName()
    {
        $cookie = new Cookie('foo');
        $this->assertSame('foo', $cookie->getName());
    }

    /** @test */
    public function itShouldGetValue()
    {
        $cookie = new Cookie('foo');
        $this->assertNull($cookie->getValue());

        $cookie = new Cookie('foo', 'bar');
        $this->assertSame('bar', $cookie->getValue());
    }

    /** @test */
    public function itShouldExpireTime()
    {
        $cookie = new Cookie('foo');
        $this->assertInternalType('int', $cookie->getExpireTime());

        $cookie = new Cookie('foo', 'bar', time() + 3600);
        $this->assertInternalType('int', $cookie->getExpireTime());

        $cookie = new Cookie('foo', 'bar', '1st January 2028');
        $this->assertInternalType('int', $cookie->getExpireTime());
    }

    /** @test */
    public function itShouldGetPath()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertSame('/', $cookie->getPath());

        $cookie = new Cookie('foo', 'bar', 0, '/user');
        $this->assertSame('/user', $cookie->getPath());
    }

    /** @test */
    public function itShouldGetDomain()
    {
        $cookie = new Cookie('foo', 'bar', 0, null, $domain = 'example.com');
        $this->assertSame($domain, $cookie->getDomain());
    }

    /** @test */
    public function itShouldBeHttpOnly()
    {
        $cookie = new Cookie('foo', 'bar', 0, null, null, false, true);
        $this->assertTrue($cookie->isHttpOnly());
    }

    /** @test */
    public function itShouldNotBeHttpOnlyByDefault()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertFalse($cookie->isHttpOnly());
    }

    /** @test */
    public function itShouldNotBeSecureByDefault()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertFalse($cookie->isSecure());
    }

    /** @test */
    public function itShouldBeSecure()
    {
        $cookie = new Cookie('foo', 'bar', 0, null, null, true);
        $this->assertTrue($cookie->isSecure());
    }

    /** @test */
    public function itMustBeStringable()
    {
        $cookie = new Cookie('foo', 'bar');

        $this->assertSame(
            'Set-Cookie: name=foo; value=bar; expires=Thursday, 01-Jan-1970 00:00:00 GMT; path=/;',
            (string)$cookie
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itCantContainInvalidCharsInNameEgEquals()
    {
        $cookie = new Cookie("foo\nbar;");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowOnInvalidTime()
    {
        $cookie = new Cookie('foo', 'bar', '1st Higgsday 2012', null, null);
    }

    /** @test */
    public function itShouldDeleteCookieIfNoValueIsSet()
    {
        $cookie = new Cookie('foo');
        $this->assertTrue($cookie->isDeleted());
    }

    /** @test */
    public function itShouldBeExpirable()
    {
        $cookie = new Cookie('foo', 'bar', time() + 60);
        $this->assertFalse($cookie->isExpired());

        $cookie->setExpired();
        $this->assertTrue($cookie->isExpired());
    }

    /** @test */
    public function itShouldBeDeleteable()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertFalse($cookie->isDeleted());

        $cookie->setDeleted();
        $this->assertTrue($cookie->isDeleted());
    }
}
