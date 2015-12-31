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

use Lucid\Writer\Object\VisibilityHelperTrait;

/**
 * @class VisibilityHelperTraitTest
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class VisibilityHelperTraitTest extends \PHPUnit_Framework_TestCase
{
    use VisibilityHelperTrait;

    /** @test */
    public function itShouldExplode()
    {
        try {
            $this->checkVisibility('translucent');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                '"translucent" is not a valid visibility, possible values are: public, protected, private.',
                $e->getMessage()
            );
        }
    }
}
