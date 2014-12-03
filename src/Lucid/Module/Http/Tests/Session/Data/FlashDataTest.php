<?php

/*
 * This File is part of the Lucid\Module\Http\Tests\Session\Data package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Tests\Session\Data;

use Lucid\Module\Http\Session\Data\FlashData;

/**
 * @class FlashDataTest
 *
 * @package Lucid\Module\Http\Tests\Session\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FlashDataTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $instance = new FlashData;
    }
}
