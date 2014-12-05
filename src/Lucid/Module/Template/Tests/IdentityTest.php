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

use Lucid\Module\Template\Identity;

/**
 * @class TemplateTest
 *
 * @package Lucid\Module\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IdentityTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Template\IdentityInterface', new Identity('name', 'type'));
    }

    /** @test */
    public function itShouldGetAttributes()
    {
        $template = new Identity('template.php', 'php');

        $this->assertSame('template.php', $template->getName());
        $this->assertSame('php', $template->getType());
    }
}
