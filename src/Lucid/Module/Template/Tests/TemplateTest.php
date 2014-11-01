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

use Lucid\Module\Template\Template;

/**
 * @class TemplateTest
 *
 * @package Lucid\Module\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Template\TemplateInterface', new Template('name', 'type'));
    }

    /** @test */
    public function itShouldGetAttributes()
    {
        $template = new Template('/path/to', 'php');

        $this->assertSame('/path/to', $template->getPath());
        $this->assertSame('/path/to', $template->getName());
        $this->assertSame('php', $template->getType());
    }
}
