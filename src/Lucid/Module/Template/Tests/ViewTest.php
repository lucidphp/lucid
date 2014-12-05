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

        $engine->shouldReceive('supports')->with('template')->andReturn(true);
        $engine->shouldReceive('render')->with('template', m::any())->andReturnUsing(function ($t, $p) {
            $this->assertTrue(isset($p['foo']));
            $this->assertTrue(isset($p['bar']));
            $this->assertTrue(isset($p['fake']));
            $this->assertTrue(isset($p['baz']));
            $this->assertTrue(isset($p['name']));
        });

        $view->render('template', $params);
    }

    /** @test */
    public function itShouldSupportTemplate()
    {
        $engine = $this->mockEngine();
        $engine->shouldReceive('supports')->with('template.php')->andReturn(true);
        $engine->shouldReceive('supports')->with('template.twig')->andReturn(false);
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
        $listener = m::mock('Lucid\Module\Template\Listener\ListenerInterface');
        $listener->shouldReceive('onRender')->andReturnUsing(function () use (&$called) {
            $called = true;
        });

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

        $listener = m::mock('Lucid\Module\Template\Listener\ListenerInterface');
        $listener->shouldReceive('onRender')->andReturnUsing(function ($data) {
            $data->add('foo', 'bar');
        });

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
        $engine = m::mock('Lucid\Module\Template\EngineInterface, Lucid\Module\Template\DisplayInterface');
        $engine->shouldReceive('supports')->with('template')->andReturn(true);
        $view = $this->newView($engine);

        $engine->shouldReceive('display')->with('template', m::any());

        $view->display('template');
    }

    /** @test */
    public function itShouldCallRenderOnNoneDisplayInterfaces()
    {
        $view = $this->newView($engine = $this->mockEngine());
        $engine->shouldReceive('supports')->with('template')->andReturn(true);

        $engine->shouldReceive('render')->with('template', m::any());

        $view->display('template');
    }

    /** @test */
    public function itShouldReturnItsEngine()
    {
        $view = $this->newView($engine = $this->mockEngine());
        $engine->shouldReceive('supports')->with('template')->andReturn(true);

        $view->getEngineForTemplate('template');
    }

    protected function newView($engine = null)
    {
        return new View($engine ?: $this->mockEngine());
    }

    protected function mockEngine()
    {
        return $e = m::mock('Lucid\Module\Template\EngineInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
