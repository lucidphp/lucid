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

use Lucid\Module\Template\DelegatingEngine;

/**
 * @class DelegatingEngineTest
 *
 * @package Lucid\Module\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DelegatingEngineTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $engine = new DelegatingEngine();

        $this->assertInstanceof('Lucid\Module\Template\EngineInterface', $engine);
        $this->assertInstanceof('Lucid\Module\Template\DisplayInterface', $engine);
    }

    /** @test */
    public function itShouldResolveEngine()
    {
        $engines = [
            $a = $this->mockEngine(),
            $b = $this->mockEngine(),
        ];

        $a->method('supports')->with('template')->willReturn(false);
        $b->method('supports')->with('template')->willReturn(true);

        $engine = new DelegatingEngine($engines);

        $this->assertTrue($engine->supports('template'));
        $this->assertSame($b, $engine->resolveEngine('template'));
    }

    /** @test */
    public function itShouldSuportTemplateTypes()
    {
        $engines = [
            $a = $this->mockEngine(),
        ];

        $map = [
            ['template.php', true],
            ['template.html', false]
        ];

        $a->method('supports')->will($this->returnValueMap($map));
        $a->method('exists')->will($this->returnValueMap($map));

        $engine = new DelegatingEngine($engines);

        $this->assertTrue($engine->supports('template.php'));
        $this->assertFalse($engine->supports('template.html'));

        $this->assertTrue($engine->exists('template.php'));
        $this->assertFalse($engine->exists('template.html'));
    }

    /** @test */
    public function itShouldDelegateRenderToEngine()
    {
        $a = $this->mockEngine();
        $a->method('supports')->with('template')->willReturn(true);
        $a->expects($this->once())->method('render');

        $engine = new DelegatingEngine([$a]);

        $engine->render('template');
    }

    /** @test */
    public function itShouldDelegateDisplayToEngine()
    {
        $a = $this->mockEngine();
        $a->method('supports')->with('template')->willReturn(true);
        $a->expects($this->once())->method('render');

        $engine = new DelegatingEngine([$a]);

        $engine->display('template');

        $a = $this->getMockBuilder('Lucid\Module\Template\Engine')->disableOriginalConstructor()->getMock();
        $a->method('supports')->with('template')->willReturn(true);
        $a->expects($this->once())->method('display');

        $engine = new DelegatingEngine([$a]);

        $engine->display('template');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionOnRenderIfNoEngineIsFoundForAGivenTamplate()
    {
        $a = $this->mockEngine();
        $a->method('supports')->with('template')->willReturn(false);

        $engine = new DelegatingEngine([$a]);
        $engine->render('template');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionOnDisplayIfNoEngineIsFoundForAGivenTamplate()
    {
        $a = $this->mockEngine();
        $a->method('supports')->with('template')->willReturn(false);

        $engine = new DelegatingEngine([$a]);
        $engine->display('template');
    }

    protected function mockEngine()
    {
        return $this->getMock('Lucid\Module\Template\EngineInterface');
    }

    protected function newEngine(array $engines = [])
    {
        return new DelegatingEngine($engines);
    }
}
