<?php

/**
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Tests\Listener;

use Lucid\Template\Tests\Stubs\Listener;

class ListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            'Lucid\Template\Listener\ListenerInterface',
            new Listener
        );
    }

    public function mockListener()
    {
        return $this->getMockbuilder('Lucid\Template\Listener\ListenerInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
