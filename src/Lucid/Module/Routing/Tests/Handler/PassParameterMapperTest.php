<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests\Handler;

use Mockery as m;
use Lucid\Module\Routing\Handler\PassParameterMapper;

/**
 * @class PassParameterMapperTest
 *
 * @package Lucid\Module\Routing\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PassParameterMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldPassParamsWithoutMapping()
    {
        $mapper = new PassParameterMapper;

        $r = m::mock('Lucid\Module\Routing\Handler\HandlerReflector');

        $res = $mapper->map($r, $p = ['a' => 1, 'b' => 2]);

        $this->assertSame(array_values($p), $res);
    }

    protected function tearDown()
    {
        m::close();
    }
}
