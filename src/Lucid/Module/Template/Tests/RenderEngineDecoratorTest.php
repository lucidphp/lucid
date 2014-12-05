<?php

/*
 * This File is part of the Lucid\Module\Template\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Tests;

use Mockery as m;
use Lucid\Module\Template\RenderEngineDecorator;

/**
 * @class RenderEngineDecoratorTest
 *
 * @package Lucid\Module\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RenderEngineDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Lucid\Module\Template\PhpRenderInterface',
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

        $engine->shouldReceive($method)->andReturnUsing(function () use (&$called, $args, $method) {
            if ($args === func_get_args()) {
                $called = true;
            } else {
                $this->fail('Arguments missmatch.');
            }
        });

        call_user_func_array([$rd, $method], $args);

        $this->assertTrue($called);
    }

    public function methodProvider()
    {
        return [
            ['extend', ['template']],
            ['insert', ['template', []]],
            ['escape', ['string']],
            ['section', ['name']],
            ['endsection', []],
            ['func', ['args']],
        ];
    }

    public function mockEngine()
    {
        return m::mock('Lucid\Module\Template\PhpRenderInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
