<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Tests\Loader;

use Lucid\Resource\Loader\AbstractLoader;

/**
 * @class AbstractLoaderTest
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class AbstractLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\Loader\LoaderInterface', $this->getLoader());
    }

    /** @test */
    public function itShouldAddAndRemoveListeners()
    {
        $loader = $this->getLoader();
        $lst = $this->mockListener();

        $loader->addListener($lst);
        $loader->expects($this->exactly(2))->method('findResource')->willReturn([$res = $this->mockResource()]);
        $loader->expects($this->exactly(2))->method('doLoad');

        $lst->expects($this->once())->method('onLoaded')->with($res);

        $loader->load('resource');
        $loader->removeListener($lst);
        $loader->load('resource');
    }

    private function mockListener()
    {
        return $this->getMockbuilder('Lucid\Resource\Loader\ListenerInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockResource()
    {
        return $this->getMockbuilder('Lucid\Resource\ResourceInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getLoader(array $methods = [])
    {
        $methods = array_merge(['supports', 'doLoad', 'findResource'], $methods);
        return $this->getMockbuilder(AbstractLoader::class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
