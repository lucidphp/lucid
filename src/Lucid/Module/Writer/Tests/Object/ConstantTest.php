<?php

/*
 * This File is part of the Lucid\Module\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Writer\Tests\Object;

use Lucid\Module\Writer\Object\Constant;

/**
 * @class ConstantTest
 * @package Lucid\Module\Writer\Tests\Object
 * @version $Id$
 */
class ConstantTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldCompileToConstnatString()
    {
        $this->assertSame('    const FOO = 12;', (new Constant('foo', '12'))->generate());
    }
}
