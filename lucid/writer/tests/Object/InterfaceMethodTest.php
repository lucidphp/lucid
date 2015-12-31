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

use Lucid\Writer\Object\InterfaceMethod;

/**
 * @class InterfaceMethodTest
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class InterfaceMethodTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldThrowExcetptionWhenSetToAbstract()
    {
        $im = new InterfaceMethod('getBar');

        try {
            $im->setAbstract(false);
        } catch (\LogicException $e) {
            $this->fail('setting abstract to false should cause no side effect.');
        }

        try {
            $im->setAbstract(true);
        } catch (\LogicException $e) {
            $this->assertSame('Cannot set interface method abstract.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExcetptionWhenBodyIsSet()
    {
        $im = new InterfaceMethod('getBar');

        try {
            $im->setBody('return null;');
        } catch (\LogicException $e) {
            $this->assertSame('Cannot set a method body on an interface method.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldWriteInterfaceMethod()
    {
        $expected = <<<PHP
    /**
     * getFoo
     *
     * @return void
     */
    public function getFoo();
PHP;
        $im = new InterfaceMethod('getFoo');

        $this->assertSame($expected, $im->generate());
    }
}
