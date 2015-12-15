<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Tests;

use Lucid\Template\Engine;
use Lucid\Template\Data\Data;
use Lucid\Template\Loader\FilesystemLoader;
use Lucid\Template\Resource\StringResource;

/**
 * @class EngineTest
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EngineTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Template\EngineInterface', new Engine($this->mockLoader()));
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
        } catch (\Lucid\Template\Exception\RenderException $e) {
            $this->assertSame('Undefined variable: dontexist', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldHandleCircularReferencesOnExtends()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));
        try {
            $engine->render('partials/extend.1.php');
        } catch (\Lucid\Template\Exception\RenderException $e) {
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
        } catch (\Lucid\Template\Exception\RenderException $e) {
            $this->assertSame('Cannot end a section. You must start a section first.', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldRegisterExtension()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));

        $func = $this->getMock('Lucid\Template\Extension\FunctionInterface');
        $func->method('getName')->willReturn('my_func');
        $ext = $this->getMock('Lucid\Template\Extension\ExtensionInterface');
        $ext->method('functions')->willReturn([$func]);

        $func->expects($this->once())->method('call');

        $engine->registerExtension($ext);

        $engine->render('func.0.php');
    }

    /** @test */
    public function itShouldUnregisterExtension()
    {
        $engine = new Engine(new FilesystemLoader(__DIR__.'/Fixures/view/'));

        $func = $this->getMock('Lucid\Template\Extension\FunctionInterface');
        $func->method('getName')->willReturn('my_func');
        $ext = $this->getMock('Lucid\Template\Extension\ExtensionInterface');
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

        $engine->setManager($view = $this->getMock('Lucid\Template\ViewManagerInterface'));
        $this->assertTrue($view === $engine->getManager());
    }

    /** @test */
    public function itShouldRenderStrings()
    {
        $id = $this->getMockbuilder('Lucid\Template\IdentityInterface')->disableOriginalConstructor()->getMock();
        $id->method('__toString')->willReturnCallback(function () {
            return 'my string';
        });
        $id->method('getType')->willReturn('string');

        $idp = $this->getMockbuilder('Lucid\Template\IdentityParserInterface')->disableOriginalConstructor()->getMock();

        $idp->method('identify')->willReturnCallback(function (...$args) use ($id) {
            return $id;
        });

        $engine = new Engine($loader = $this->mockLoader(), $idp);
        $engine->addType('string');

        $loader->method('load')->willReturn(new StringResource('my string'));

        $engine->render('my string');
    }

    /** @test */
    public function itShouldNotifyListener()
    {
        $data = new Data;
        $data->set(['foo' => 'bar']);
        $engine = new Engine($loader = $this->mockLoader());
        $loader->method('load')->willReturn($this->mockResource());
        $engine->setManager($view = $this->mockViewManager());

        $view->expects($this->once())->method('notifyListeners')->with('template.php');
        $view->expects($this->once())->method('flushData')->with('template.php')->willReturn($data);

        $engine->render('template.php');
    }

    protected function newEngine()
    {
        $engine = new Engine($this->loader = $this->mockLoader());

        return $engine;
    }

    protected function mockViewManager()
    {
        return $this->getMockbuilder('Lucid\Template\ViewManagerInterface')->disableOriginalConstructor()->getMock();
    }


    protected function mockLoader()
    {
        return $this->getMockbuilder('Lucid\Template\Loader\LoaderInterface')->disableOriginalConstructor()->getMock();
    }

    protected function mockResource()
    {
        return $this->getMockbuilder('Lucid\Template\Resource\ResourceInterface')->disableOriginalConstructor()->getMock();
    }
}
