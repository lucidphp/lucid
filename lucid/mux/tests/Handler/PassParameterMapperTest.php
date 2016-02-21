<?php

/*
 * This File is part of the Lucid\Mux\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Tests\Handler;

use Lucid\Mux\Handler\PassParameterMapper;

/**
 * @class PassParameterMapperTest
 *
 * @package Lucid\Mux\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PassParameterMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldPassParamsWithoutMapping()
    {
        $mapper = new PassParameterMapper;

        $r = $this->getMockBuilder('Lucid\Mux\Handler\Reflector')
            ->disableOriginalConstructor()
            ->getMock();

        $res = $mapper->map($r, $p = ['a' => 1, 'b' => 2]);

        $this->assertSame(array_values($p), $res);
    }
}
