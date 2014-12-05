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

use Lucid\Module\Template\Engine;
use Lucid\Module\Template\Loader\FilesystemLoader;

/**
 * @class EngineTest
 *
 * @package Lucid\Module\Template\Tests
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
