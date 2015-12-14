<?php

/*
 * This File is part of the Lucid\Template\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Tests;

use Lucid\Template\RenderEngineDecorator;

/**
 * @class RenderEngineDecoratorTest
 *
 * @package Lucid\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RenderEngineDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Lucid\Template\PhpRenderInterface',
            new RenderEngineDecorator($this->mockEngine())
        );
    }

    /**
     * @test
     * @dataProvider methodProvider
     */
    public function itShouldDelegateFunc($method, array $args)
    {
        $rd = new RenderEngineDecorator($engine = $this->mockEngine());
        $called = false;

        $engine->method('func')->willReturn(null);
        $engine->method($method)->will($this->returnCallback(function (...$arguments) use (&$called, $args, $method) {
            if ($args === $arguments) {
                $called = true;
            } else {
                $this->fail('Arguments missmatch.');
            }
        }));

        call_user_func_array([$rd, $method], $args);

        $this->assertTrue($called);
    }

    public function methodProvider()
    {
        return [
            ['extend', ['template', []]],
            ['insert', ['template', []]],
            ['escape', ['string']],
            ['section', ['name']],
            ['endsection', []],
            ['func', ['args']],
        ];
    }

    public function mockEngine()
    {
        return $this->getMockBuilder('Lucid\Template\PhpRenderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
