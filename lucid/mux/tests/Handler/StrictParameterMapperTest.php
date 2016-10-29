<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Tests\Handler;

use Lucid\Mux\Handler\StrictParameterMapper as Mapper;

/**
 * @class StrictParameterMapperTest
 *
 * @package Lucid\Mux\Tests\Handler
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
        } catch (\UnexpectedValueException $e) {
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

        $tm->method('has')->with('PHPUnit_Framework_TestCase')->willReturn(true);
        $tm->method('get')->with('PHPUnit_Framework_TestCase')->willReturn($this);

        $res = $mapper->map($r, []);

        $this->assertSame([$this], $res);
    }

    /** @test */
    public function itShouldThrowIfTypeIsMissing()
    {
        $r = $this->mockReflector(function (\PHPUnit_Framework_TestCase $val) {
        });

        $mapper = new Mapper($tm = $this->mockTypes());

        $tm->method('has')->with('PHPUnit_Framework_TestCase')->willReturn(false);

        try {
            $mapper->map($r, []);
        } catch (\UnexpectedValueException $e) {
            $this->assertSame('Cannot map class "PHPUnit_Framework_TestCase" to parameter "{$val}".', $e->getMessage());
            return;
        }

        $this->fail('Test should have thrown exception.');
    }

    protected function mockTypes()
    {
        return $this->getMockbuilder('Lucid\Mux\Handler\TypeMapCollectionInterface')
            ->disableOriginalConstructor()->getMock();
    }

    protected function mockTypeMapper()
    {
        return $this->getMockbuilder('Lucid\Mux\Hander\TypeMapperInterface')
            ->disableOriginalConstructor()->getMock();
    }

    protected function mockReflector(\Closure $fn)
    {
        $r = $this->getMockBuilder('Lucid\Mux\Handler\Reflector')
            ->disableOriginalConstructor()->getMock();

        $r->method('getReflector')->willReturn(new \ReflectionFunction($fn));

        return $r;
    }
}
