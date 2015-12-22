<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Tests\Extension;

use Lucid\Template\Extension\PhpEngineExtension;

/**
 * @class PhpEngineExtensionTest
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpEngineExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Template\Extension\ExtensionInterface', new PhpEngineExtension);
    }

    /** @test */
    public function itShouldReturnEngine()
    {
        $ext = new PhpEngineExtension;
        $this->assertNull($ext->getEngine());

        $ext->setEngine($engine = $this->mockEngine());

        $this->assertTrue($engine === $ext->getEngine());
    }

    /** @test */
    public function itShouldThrowIfEngineIsNotSupported()
    {
        $ext = new PhpEngineExtension;
        $engine = $this->getMockbuilder('Lucid\Template\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        try {
            $ext->setEngine($engine);
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldExportPhpRenderInterfaceMethods()
    {
        $ext = new PhpEngineExtension;
        $ext->setEngine($this->mockEngine());

        $ref = new \ReflectionClass('Lucid\Template\PhpRenderInterface');
        $methods = array_map(function ($m) {
            return $m->getName();
        }, $ref->getMethods());

        $this->assertTrue(count($methods) === count($ext->functions()));

        foreach ($ext->functions() as $func) {
            $this->assertTrue(in_array($func->getName(), $methods));
        }
    }

    private function mockEngine()
    {
        return $this->getMockbuilder('Lucid\Template\Engine')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
