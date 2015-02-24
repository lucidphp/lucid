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

use Lucid\Module\Template\View;

/**
 * @class ViewTest
 *
 * @package Lucid\Module\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldPassGlobals()
    {
        $engine = $this->mockEngine();
        $view = $this->newView($engine);

        $view->setGlobals(['foo' => 'bar', 'baz' => 'bam']);
        $view->addGlobals(['bar' => 'zzz']);
        $view->addGlobal('fake', 'name');

        $params = ['name' => 'Carl'];

        $engine->method('supports')->with('template')->willReturn(true);
        $engine->expects($this->once())->method('render')->will($this->returnCallback(function ($t, $p) {
            $this->assertTrue(isset($p['foo']));
            $this->assertTrue(isset($p['bar']));
            $this->assertTrue(isset($p['fake']));
            $this->assertTrue(isset($p['baz']));
            $this->assertTrue(isset($p['name']));
        }));

        $view->render('template', $params);
    }

    /** @test */
    public function itShouldSupportTemplate()
    {
        $engine = $this->mockEngine();

        $map = [
            ['template.php', true],
            ['template.twig', false]
        ];

        $engine->method('supports')->will($this->returnValueMap($map));
        $view = $this->newView($engine);

        $this->assertTrue($view->supports('template.php'));
        $this->assertFalse($view->supports('template.twig'));
    }

    /** @test */
    public function itShouldSetListeners()
    {
        $engine = $this->mockEngine();
        $view = $this->newView($engine);

        $called = false;
        $listener = $this->getMock('Lucid\Module\Template\Listener\ListenerInterface');
        $listener->method('onRender')->will($this->returnCallback(function () use (&$called) {
            $called = true;
        }));

        $view->setListeners(['template' => $listener]);
        $view->notifyListeners('template');
        $this->assertTrue($called);

        $called = false;
        $view->notifyListeners('new.template');
        $this->assertFalse($called);

    }

    /** @test */
    public function itShouldBeAbleToFlushData()
    {
        $engine = $this->mockEngine();
        $view = $this->newView($engine);

        $listener = $this->getMock('Lucid\Module\Template\Listener\ListenerInterface');
        $listener->method('onRender')->will($this->returnCallback(function ($data) {
            $data->add('foo', 'bar');
        }));

        $view->addListener('template', $listener);

        $view->notifyListeners('template');
        $data = $view->flushData('template');
        $this->assertInstanceof('Lucid\Module\Template\Data\TemplateDataInterface', $data);
        $this->assertArrayHasKey('foo', $data->all());
        $this->assertFalse($view->flushData('template'));
    }

    /** @test */
    public function itShouldCallDisplayOnDisplayInterfaces()
    {
        $engine = $this->getMock('Lucid\Module\Template\Tests\Stubs\Displayable');
        $engine->method('supports')->with('template')->willReturn(true);
        $view = $this->newView($engine);

        $engine->expects($this->once())->method('display')->will($this->returnCallback(function ($name) {
            if ('template' !== $name) {
                $this->fail();
            }
        }));

        $view->display('template');
    }

    /** @test */
    public function itShouldCallRenderOnNoneDisplayInterfaces()
    {
        $view = $this->newView($engine = $this->mockEngine());
        $engine->method('supports')->with('template')->willReturn(true);

        $engine->expects($this->once())->method('render')->will($this->returnCallback(function () {
            $args = func_get_args();
            if ('template' !== $args[0]) {
                $this->fail();
            }
        }));

        $view->display('template');
    }

    /** @test */
    public function itShouldReturnItsEngine()
    {
        $view = $this->newView($engine = $this->mockEngine());
        $engine->method('supports')->with('template')->willReturn(true);

        $view->getEngineForTemplate('template');
    }

    protected function newView($engine = null)
    {
        return new View($engine ?: $this->mockEngine());
    }

    protected function mockEngine()
    {
        return $this->getMockBuilder('Lucid\Module\Template\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
