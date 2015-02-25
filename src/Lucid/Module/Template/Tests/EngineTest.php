<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Tests;

use Lucid\Module\Template\Engine;
use Lucid\Module\Template\Loader\FilesystemLoader;

/**
 * @class EngineTest
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EngineTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Template\EngineInterface', new Engine($this->mockLoader()));
    }

    /** @test */
    public function itShouldTellIfTemplateExists()
    {
        $engine = $this->newEngine();

        $map = [
            ['template.php', true],
            ['template.html', false],
        ];

        $this->loader->expects($this->any())
            ->method('supports')->will($this->returnValueMap($map));

        $this->loader->expects($this->once())
            ->method('load')->with('template.php')->willReturn($this->mockResource());

        $this->assertTrue($engine->exists('template.php'));
        $this->assertFalse($engine->exists('template.html'));
    }

    /** @test */
    public function renderShouldReturnString()
    {
        $engine = $this->newEngine();

        $this->loader->method('load')->willReturn($this->mockResource());

        $this->assertInternalType('string', $engine->render('template.php'));
    }

    /** @test */
    public function itShouldSupportItsType()
    {
        $engine = $this->newEngine();

        $this->assertTrue($engine->supports('template.php'));
        $this->assertFalse($engine->supports('html'));
    }

    /** @test */
    public function itShouldDisplayTemplate()
    {
        $engine = $this->newEngine();

        $this->loader->method('load')->willReturn($this->mockResource());
        ob_start();
        $engine->display('template.php');
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertInternalType('string', $content);
    }

    /** @test */
    public function itShouldRegisterGlobalData()
    {
        $engine = $this->newEngine();
        $engine->setGlobals(['foo' => 'bar']);

        $this->assertInternalType('array', $engine->getGlobals());
        $this->assertArrayHasKey('foo', $engine->getGlobals());
        $engine->addGlobal('bar', 'baz');
        $this->assertArrayHasKey('bar', $engine->getGlobals());
    }

    /** @test */
    public function itShouldHandleTemplateErrors()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));

        try {
            $engine->render('error.php');
        } catch (\Lucid\Module\Template\Exception\RenderException $e) {
            $this->assertSame('Undefined variable: dontexist', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldHandleCircularReferencesOnExtends()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));
        try {
            $engine->render('partials/extend.1.php');
        } catch (\Lucid\Module\Template\Exception\RenderException $e) {
            $this->assertSame('Circular reference in partials/extend.1.php.', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldThrowExceptionOnFaultySections()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));
        try {
            $engine->render('index.1.php');
        } catch (\Lucid\Module\Template\Exception\RenderException $e) {
            $this->assertSame('Cannot end a section. You must start a section first.', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldRegisterExtension()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));

        $func = $this->getMock('Lucid\Module\Template\Extension\FunctionInterface');
        $func->method('getName')->willReturn('my_func');
        $ext = $this->getMock('Lucid\Module\Template\Extension\ExtensionInterface');
        $ext->method('functions')->willReturn([$func]);

        $func->expects($this->once())->method('call');

        $engine->registerExtension($ext);

        $engine->render('func.0.php');
    }

    /** @test */
    public function itShouldUnregisterExtension()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));

        $func = $this->getMock('Lucid\Module\Template\Extension\FunctionInterface');
        $func->method('getName')->willReturn('my_func');
        $ext = $this->getMock('Lucid\Module\Template\Extension\ExtensionInterface');
        $ext->method('functions')->willReturn([$func]);

        $func->expects($this->once())->method('call');

        $engine->registerExtension($ext);

        $engine->render('func.0.php');

        $engine->removeExtension($ext);

        try {
            $engine->render('func.0.php');
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);

            return;
        }

        $this->fail();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function callingUnknowenFuncShouldThrowException()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));
        $engine->render('func.1.php');
    }

    /** @test */
    public function viewShouldBeSettable()
    {
        $engine = $this->newEngine();

        $engine->setManager($view = $this->getMock('Lucid\Module\Template\ViewManagerInterface'));
        $this->assertSame($view, $engine->getManager());
    }

    protected function newEngine()
    {
        $engine = new Engine($this->loader = $this->mockLoader());

        return $engine;
    }

    protected function mockLoader()
    {
        return $this->getMock('Lucid\Module\Template\Loader\LoaderInterface');
    }

    protected function mockResource()
    {
        return $this->getMock('Lucid\Module\Template\Resource\ResourceInterface');
    }
}
