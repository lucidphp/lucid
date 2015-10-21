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

use Lucid\Http\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
         $this->assertInstanceof('Psr\Http\Message\UriInterface', new Url('foo/bar'));
    }

    /** @test */
    public function itIsExpectedThat()
    {

    }
}
