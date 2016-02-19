<?php

/*
 * This File is part of the Lucid\Package package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package\Tests;

use Lucid\Package\AbstractRepository;

/**
 * @class AbstractRepositoryTest
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class AbstractRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Package\RepositoryInterface', $this->mockRepository());
    }

    /** @test */
    public function itShouldAddProviders()
    {
        $ps = [
            'p1' => $this->mockProvider(),
            'p2' => $this->mockProvider(),
        ];

        extract($ps);

        $p1->method('getAlias')->willReturn('a');
        $p1->method('getName')->willReturn('aProvider');
        $p2->method('getAlias')->willReturn('b');
        $p2->method('getName')->willReturn('bProvider');

        $rep = $this->mockRepository();
        $rep->addProviders($ps);
    }

    private function mockProvider()
    {
        return  $this->getMockbuilder('Lucid\Package\ProviderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockRepository(array $methods = [])
    {
        return $this->getMock('Lucid\Package\AbstractRepository', $methods);
    }
}
