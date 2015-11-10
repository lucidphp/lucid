<?php

/**
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests;

use Lucid\Http\Uri;

class UriTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
         $this->assertInstanceof('Psr\Http\Message\UriInterface', new Uri('foo/bar'));
    }

    /** @test */
    public function itShouldHandleHostDescription()
    {
        //$url = new Uri('foo/bar');

        //$this->assertEquals('localhost', $url->getHost());

        //$url = new Uri('foo/bar');
        //$url->withHost('myserver');

        //$this->assertEquals('myserver', $url->getHost());
    }

    /** @test */
    public function itShouldHandleWharever()
    {
        //$url = new Uri('foo/bar');
        //$url->withUserInfo('me', 'passphrase');

        //$this->assertEquals('me:passphrase', $url->getUserInfo());
    }
}
