<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests\Parameter;

use Lucid\DI\Parameter\Value;

/**
 * @class ValueTest
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeStringable()
    {
        $v = new Value(10);

        $this->assertSame('10', (string)$v);
    }
}
