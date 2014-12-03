<?php

/*
 * This File is part of the Lucid\Module\Http\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Tests;

use Lucid\Module\Http\Request;

/**
 * @class RequestTest
 *
 * @package Lucid\Module\Http\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Http\Request', new Request);
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $req = new Request(['foo' => 'bar'], [], [], [], [], ['QUERY_STRING' => 'bar=baz']);
        $req->getQueryParams();
        $req->getProtocolVersion();

        //$res = fopen('php://input', 'rb');

        //var_dump(stream_get_contents($res));
        //var_dump($req);
        //fclose($res);
    }
}
