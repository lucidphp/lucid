<?php

/*
 * This File is part of the Lucid\Template\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Tests;

use Lucid\Template\IdentityParser;

/**
 * @class TemplateTest
 *
 * @package Lucid\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IdentityParserTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Template\IdentityParserInterface', new IdentityParser);
    }

    /** @test */
    public function itShouldSetAttributes()
    {
        $parser = new IdentityParser;

        $this->assertInstanceOf('Lucid\Template\IdentityInterface', $idt = $parser->identify('template.php'));
        $this->assertSame('php', $idt->getType());
        $this->assertSame('template.php', $idt->getName());

        $idt = $parser->identify('template');
        $this->assertNull($idt->getType());
        $this->assertSame('template', $idt->getName());
    }

    /** @test */
    public function itShouldReturnGivenIdentities()
    {
        $parser = new IdentityParser;
        $mock = $this->getMock('Lucid\Template\IdentityInterface');

        $this->assertTrue($mock === $parser->identify($mock));
    }
}
