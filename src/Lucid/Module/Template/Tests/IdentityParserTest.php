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

use Lucid\Module\Template\IdentityParser;

/**
 * @class TemplateTest
 *
 * @package Lucid\Module\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IdentityParserTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Template\IdentityParserInterface', new IdentityParser);
    }

    /** @test */
    public function itShouldSetAttributes()
    {
        $parser = new IdentityParser;

        $this->assertInstanceOf('Lucid\Module\Template\IdentityInterface', $idt = $parser->identify('template.php'));
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
        $mock = $this->getMock('Lucid\Module\Template\IdentityInterface');

        $this->assertSame($mock, $parser->identify($mock));
    }
}
