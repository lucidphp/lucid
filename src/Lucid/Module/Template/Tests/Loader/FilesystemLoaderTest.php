<?php

/*
 * This File is part of the Lucid\Module\Template\Tests\Loader package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Tests\Loader;

use Lucid\Module\Template\Loader\FilesystemLoader;

/**
 * @class FilesystemLoaderTest
 *
 * @package Lucid\Module\Template\Tests\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesystemLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Lucid\Module\Template\Loader\LoaderInterface',
            new FilesystemLoader(__DIR__.'/../Fixures/view')
        );
    }

    /** @test */
    public function itShouldLoadTemplates()
    {
        $id = $this->mockIdentity();
        $id->expects($this->any())->method('getName')->willReturn('index.php');

        $loader = new FilesystemLoader(__DIR__.'/../Fixures/view');

        $loader->load($id);
    }

    /** @test */
    public function itShouldCacheResolvedResources()
    {
        $id = $this->mockIdentity();
        $id->expects($this->any())->method('getName')->willReturn('index.php');

        $loader = new FilesystemLoader(__DIR__.'/../Fixures/view');

        $res = $loader->load($id);

        $this->assertSame($res, $loader->load($id));
    }

    /** @test */
    public function itShouldValidateTemplateTimestamps()
    {
        $idA = $this->mockIdentity();
        $idA->expects($this->any())->method('getName')->willReturn('index.php');
        $idB = $this->mockIdentity();
        $idB->expects($this->any())->method('getName')->willReturn('index');
        $loader = new FilesystemLoader(__DIR__.'/../Fixures/view');

        $this->assertTrue($loader->isValid($idA, time()));
        $this->assertFalse($loader->isValid($idA, 1000));

        $this->assertFalse($loader->isValid($idB, time()));
    }

    /** @test */
    public function itShouldResolveResourceIfNameIsAbsolutePath()
    {
        $id = $this->mockIdentity();
        $id->expects($this->any())->method('getName')->willReturn(__DIR__.'/../Fixures/view/index.php');

        $loader = new FilesystemLoader('/');

        $this->assertInstanceof('Lucid\Module\Template\Resource\FileResource', $loader->load($id));
    }

    /**
     * @test
     * @expectedException \Lucid\Module\Template\Exception\LoaderException
     */
    public function itShouldThrowExceptionIfPathCannotBeResolved()
    {
        $id = $this->mockIdentity();
        $id->expects($this->any())->method('getName')->willReturn('index');
        $loader = new FilesystemLoader(__DIR__.'/../Fixures/view');
        $loader->load($id);
    }

    protected function mockIdentity()
    {
        return $this->getMock('Lucid\Module\Template\IdentityInterface');
    }
}
