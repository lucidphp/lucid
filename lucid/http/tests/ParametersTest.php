<?php

/**
 * This File is part of the Lucid\Http\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests;

use Lucid\Http\Parameters;

class ParametersTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Http\ParameterInterface', $this->newParams([]));
    }

    /** @test */
    public function itShouldGetParametersByKey()
    {
        $params = $this->newParams(['key' => 'value']);

        $this->assertSame('value', $params->get('key'));
    }

    /** @test */
    public function itShouldTellIfParameterExists()
    {
        $params = $this->newParams(['key' => 'value']);

        $this->assertTrue($params->has('key'));
        $this->assertFalse($params->has('foo'));
    }

    /** @test */
    public function itShouldGetParameterKeys()
    {
        $params = $this->newParams(['foo' => 'value', 'bar' => 'value']);
        $this->assertSame(['foo', 'bar'], $params->keys());
    }

    /** @test */
    public function itShouldGetParameterAsArray()
    {
        $params = $this->newParams($all = ['foo' => 'value', 'bar' => 'value']);
        $this->assertSame($all, $params->all());
    }

    protected function newParams(array $params = [])
    {
        return new Parameters($params);
    }
}
