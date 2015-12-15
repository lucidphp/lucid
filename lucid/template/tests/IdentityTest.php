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

use Lucid\Template\Identity;

/**
 * @class TemplateTest
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IdentityTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Template\IdentityInterface', new Identity('name', 'type'));
    }

    /** @test */
    public function itShouldGetAttributes()
    {
        $template = new Identity('template.php', 'php');

        $this->assertSame('template.php', $template->getName());
        $this->assertSame('php', $template->getType());
    }
}
