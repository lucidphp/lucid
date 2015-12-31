<?php

/**
 * This File is part of the Lucid\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Tests\Object;

use Lucid\Writer\Object\Property;

/**
 * @class PropertyTest
 * @package Lucid\Writer
 * @version $Id$
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldCreateAClassProperty()
    {
        $prop = new Property('foo');

        $expected = <<<PHP
    /** @var mixed */
    public \$foo;
PHP;
        $this->assertSame($expected, (string)$prop);
    }

    /** @test */
    public function itShouldHaveAnInitialValueAndType()
    {
        $prop = new Property('foo');

        $expected = <<<PHP
    /** @var string */
    private \$foo = 'foo';
PHP;
        $prop->setValue("'foo'");
        $prop->setType(Property::T_STRING);
        $prop->setVisibility(Property::IS_PRIVATE);
        $this->assertSame($expected, (string)$prop);
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $expected = <<<PHP
    /**
     * Acme\Foo\Wahtever
     *
     * @var mixed
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public \$id;
PHP;
        $prop = new Property('id');

        $prop->setDescription('Acme\Foo\Wahtever');
        $prop->addAnnotation('ORM\Column(type="integer")');
        $prop->addAnnotation('ORM\Id');
        $prop->addAnnotation('ORM\GeneratedValue(strategy="AUTO")');

        $this->assertSame($expected, (string)$prop);
    }
}
