<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Tests\Loader;

use Lucid\Template\Loader\LoggerAwareLoader as Loader;

/**
 * @class LoggerAwareLoaderTest
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LoggerAwareLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            'Lucid\Template\Loader\LoaderInterface',
            new Loader($this->mockLoader(), $this->mockLogger())
        );
    }

    /** @test */
    public function itShouldLogLoadingATemplate()
    {
        $loader = new Loader($ml = $this->mockLoader(), $logger = $this->mockLogger());

        $id = $this->mockId();
        $ml->expects($this->once())->method('load')->with($id)->willReturn($this->mockResource());
        $logger->expects($this->once())->method('info');
        $loader->load($id);
    }

    /** @test */
    public function itShouldLogErrorLoadingATemplate()
    {
        $loader = new Loader($ml = $this->mockLoader(), $logger = $this->mockLogger());

        $id = $this->mockId();
        $ml->expects($this->once())->method('load')->with($id)->willReturn(null);
        $logger->expects($this->once())->method('error');
        $loader->load($id);
    }

    /** @test */
    public function itShouldPassEncapsulatingMethods()
    {
        $loader = new Loader($ml = $this->mockLoader(), $this->mockLogger());
        $ml->expects($this->once())->method('isValid')->with($id = $this->mockId(), $now = time());
        $loader->isValid($id, $now);
    }

    private function mockResource()
    {
        return $this->getMockbuilder('Lucid\Template\Resource\ResourceInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
    private function mockId()
    {
        return $this->getMockbuilder('Lucid\Template\IdentityInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockLoader()
    {
        return $this->getMockbuilder('Lucid\Template\Loader\LoaderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockLogger()
    {
        return $this->getMockbuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
