<?php

/*
 * This File is part of the Lucid\Adapter\Twig package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Twig\Tests;

use Lucid\Adapter\Twig\TwigEngine;

/**
 * @class TwigEngineTest
 *
 * @package Lucid\Adapter\Twig
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TwigEngineTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Template\EngineInterface', $engine = new TwigEngine($this->mockEnv()));
        $this->assertInstanceof('Lucid\Template\DisplayInterface', $engine);
        $this->assertInstanceof('Lucid\Template\ViewAwareInterface', $engine);
    }

    /** @test */
    public function itShouldAutoSetTemplateBaseClass()
    {
        $env = $this->mockEnv();
        $env->method('setBaseTemplateClass')->with('Lucid\Adapter\Twig\TwigTemplate')->willReturn(null);
        $engine = new TwigEngine($env);
    }

    /** @test */
    public function itIsExpectedThat()
    {
    }

    /** @test */
    public function itShouldSupportTwig()
    {
        $engine = $this->newEngine($env = $this->mockEnv());
        $this->assertTrue($engine->supports('template.twig'));
        $this->assertTrue($engine->supports($this->mockTemplate($env)));
    }

    /** @test */
    public function itShouldRenderOnEnv()
    {
        $context = [1, 2];
        $engine = $this->newEngine($env = $this->mockEnv());
        $env->method('loadTemplate')->with('template.twig')->willReturn($template = $this->mockTemplate($env));

        $template->method('display')->willReturn('ok');

        //$this->assertSame('ok', $engine->render('template.twig'));
        $engine->render('template.twig');
    }

    /**
     * newEngine
     *
     * @param \Twig_Environment $env
     *
     * @return void
     */
    protected function newEngine(\Twig_Environment $env = null)
    {
        return new TwigEngine($env ?: $this->mockEnv());
    }

    /**
     * mockEnv
     *
     * @param mixed $template
     *
     * @return void
     */
    protected function mockEnv($template = null)
    {
        $mock = $this->getMock('Lucid\Adapter\Twig\TwigEnvironment');
        $mock->method('loadTemplate')->willReturn($template ?: $this->mockTemplate($mock));

        return $mock;
    }

    /**
     * mockTemplate
     *
     * @param \Twig_Environment $env
     * @param string $name
     *
     * @return void
     */
    protected function mockTemplate(\Twig_Environment $env = null, $name = 'template.twig')
    {
        $mock = $this->getMockBuilder('Lucid\Adapter\Twig\TwigTemplate')
            ->setMethods(['doDisplay', 'getTemplateName', 'getEnvironment', 'mergeGlobals', 'display'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->method('getEnvironment')->willReturn($env ?: $this->mockEnv());
        $mock->method('getTemplateName')->willReturn($name);

        return $mock;
    }
}
