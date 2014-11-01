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

        $this->assertTrue(is_string($engine->render('template')));
    }

    /** @test */
    public function itShouldSupportItsType()
    {
        $engine = $this->newEngine();

        $this->assertTrue($engine->supports('php'));
        $this->assertFalse($engine->supports('html'));
    }

    /** @test */
    public function itShouldGetItsType()
    {
        $engine = $this->newEngine();

        $this->assertSame('php', $engine->getType());
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
