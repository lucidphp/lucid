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
use Lucid\Module\Routing\Handler\StrictParameterMapper as Mapper;

/**
 * @class StrictParameterMapperTest
 *
 * @package Lucid\Module\Routing\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StrictParameterMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function shouldHandleStrictParams()
    {
        $r = $this->mockReflector(function ($b, $a) {
        });

        $mapper = new Mapper;

        $res = $mapper->map($r, ['a' => 'foo', 'b' => 'bar']);

        $this->assertSame(['bar', 'foo'], $res);
    }

    /** @test */
    public function shouldThrowExceptionIfParamIsMissing()
    {
        $r = $this->mockReflector(function ($b, $a) {
        });

        $mapper = new Mapper;

        try {
            $mapper->map($r, ['a' => 'foo']);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Missing non optional parameter "{$b}".', $e->getMessage());

            return;
        }

        $this->fail('Test should have thrown exception.');
    }

    /** @test */
    public function shouldNotThrowExceptionIfParamIsMissingIsOptional()
    {
        $r = $this->mockReflector(function ($b, $a = null) {
        });

        $mapper = new Mapper;
        $res = $mapper->map($r, ['b' => 'bar']);

        $this->assertSame(['bar', null], $res);
    }

    /** @test */
    public function itShouldAskTypeMapper()
    {
        $r = $this->mockReflector(function (\PHPUnit_Framework_TestCase $val) {
        });

        $mapper = new Mapper($tm = $this->mockTypes());

        $tm->shouldReceive('has')->with('PHPUnit_Framework_TestCase')->andReturn(true);
        $tm->shouldReceive('get')->with('PHPUnit_Framework_TestCase')->andReturn($this);

        $res = $mapper->map($r, []);

        $this->assertSame([$this], $res);
    }

    /** @test */
    public function itShouldThrowIfTypeIsMissing()
    {
        $r = $this->mockReflector(function (\PHPUnit_Framework_TestCase $val) {
        });

        $mapper = new Mapper($tm = $this->mockTypes());

        $tm->shouldReceive('has')->with('PHPUnit_Framework_TestCase')->andReturn(false);

        try {
            $mapper->map($r, []);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Cannot map class "PHPUnit_Framework_TestCase" to parameter "{$val}".', $e->getMessage());
            return;
        }

        $this->fail('Test should have thrown exception.');
    }

    protected function mockTypes()
    {
        $m = m::mock('Lucid\Module\Routing\Handler\TypeMapCollectionInterface');

        return $m;
    }

    protected function mockTypeMapper()
    {
        $m = m::mock('Lucid\Module\Routing\Hander\TypeMapperInterface');

        return $m;
    }

    protected function mockReflector(\Closure $fn)
    {
        $r = m::mock('Lucid\Module\Routing\Handler\HandlerReflector');

        $r->shouldReceive('getReflector')->andReturn(new \ReflectionFunction($fn));

        return $r;
    }

    protected function tearDown()
    {
        m::close();
    }
}
