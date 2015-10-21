<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Tests\Object;

use Lucid\Writer\Object\Constant;

/**
 * @class ConstantTest
 * @package Lucid\Writer\Tests\Object
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
