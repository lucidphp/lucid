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

use Lucid\Http\ParametersMutable;

class ParametersMutableTest extends ParametersTest
{
    /** @test */
    public function itShouldBeAbleToAddParams()
    {
        $params = $this->newParams();
        $params->add('key', 'value');

        $this->assertTrue($params->has('key'));
    }

    /** @test */
    public function itShouldRemoveItems()
    {
        $params = $this->newParams(['foo' => 'bar', 'bar' => 'baz']);

        $this->assertTrue($params->has('foo'));
        $this->assertTrue($params->has('bar'));
        $params->remove('foo');
        $this->assertFalse($params->has('foo'));
    }

    protected function newParams(array $params = [])
    {
        return new ParametersMutable($params);
    }
}
