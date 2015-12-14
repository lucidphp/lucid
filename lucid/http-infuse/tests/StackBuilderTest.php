<?php

/*
 * This File is part of the lucid/http-infuse package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Infuse\Tests;

use Lucid\Http\Infuse\StackBuilder;

/**
 * @class StackBuilderTest
 *
 * @package lucid/http-infuse
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StackBuilderTest extends \PHPUnit_Framework_TestCase
{
    use TestHelperTrait;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Http\Infuse\StackBuilder', new StackBuilder($this->mockHttpDispatcher(), []));
    }

    /** @test */
    public function makeShouldReturnInstanceOfStack()
    {
        $builder = new StackBuilder($this->mockHttpDispatcher());
        $this->assertInstanceOf('Lucid\Http\Infuse\Stack', $builder->make());
    }
}
