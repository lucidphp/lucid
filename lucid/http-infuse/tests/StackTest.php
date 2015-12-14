<?php

/**
 * This File is part of the lucid/http-infuse package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Infuse\Tests;

use Lucid\Http\Infuse\Stack;
use SebastianBergmann\PeekAndPoke\Proxy;

/**
 * @class StackTest
 *
 * @package lucid/http-infuse
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StackTest extends \PHPUnit_Framework_TestCase
{
    use TestHelperTrait;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Http\Infuse\Stack', new Stack($this->mockHttpDispatcher()));
    }

    /** @test */
    public function itShoudHoldTheOriginalDispatcher()
    {
        $stack = new Stack($rqd = $this->mockHttpDispatcher());
        $proxy = new Proxy($stack);

        $this->assertTrue($rqd === $proxy->getDispatcher());
    }

    /** @test */
    public function itShouldHandleOnOriginalDispatcher()
    {
        $req = $this->mockRequest();
        $res = $this->mockResponse();

        $stack = new Stack($rqd = $this->mockHttpDispatcher());
        $rqd->method('handle')->with($req)->willReturnCallback(function () use ($res) {
            return $res;
        });

        $this->assertTrue($res === $stack->handle($req));
    }
}
