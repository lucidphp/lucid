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

use Lucid\Adapter\Twig\TwigEnvironment;

/**
 * @class TwigEnvironmentTest
 *
 * @package Lucid\Adapter\Twig
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TwigEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Twig_Environment', $env = new TwigEnvironment);
        $this->assertInstanceof('Lucid\Template\ViewAwareInterface', $env);
    }
}
