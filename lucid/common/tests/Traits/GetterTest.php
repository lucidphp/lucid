<?php

namespace Lucid\Common\Tests\Traits;

use Lucid\Common\Traits\Getter;

class GetterTest extends \PHPUnit_Framework_TestCase
{
    use Getter;

    /**
     * @test
     */
    public function testGetDefault()
    {
        $p = ['foo' => 'bar'];
        $this->assertSame('bar', $this->getDefault($p, 'foo'));
        $this->assertNull($this->getDefault($p, 'bar'));
    }

    /**
     * @test
     */
    public function testGetStrictDefault()
    {
        $p = ['foo' => null];

        $this->assertNull($this->getStrictDefault($p, 'foo'));
        $this->assertSame('baz', $this->getStrictDefault($p, 'bar', 'baz'));
    }

    /**
     * @test
     */
    public function testGetDefaultUsing()
    {
        $p = ['foo' => 'bar'];
        $this->assertSame('bar', $this->getDefaultUsing($p, 'foo', function () {
            return 'bar';
        }));

        $this->assertSame('baz', $this->getDefaultUsing([], 'bar', function () {
            return 'baz';
        }));
    }

    /**
     * @test
     */
    public function testGetStrictDefaultUsing()
    {
        $p = ['foo' => null];
        $this->assertSame('bar', $this->getStrictDefaultUsing($p, 'foo', function () {
            return 'bar';
        }));

        //$this->assertSame('baz', $this->getStrictDefaultUsing([], 'bar', function () {
            //return 'baz';
        //}));
    }
}
