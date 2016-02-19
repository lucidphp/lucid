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

use Lucid\Package\Dependency;
use Lucid\Package\Exception\RequirementException;

/**
 * @class DependencyTest
 *
 * @package Lucid\Package\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DependencyTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldExplodeOnCircularRefs()
    {
        $dep = new Dependency;
        $ps = [
            'p1' => ['foo'],
            'foo' => ['p1']
        ];

        $nn = $this->getProviderMocks($ps);

        extract($nn);

        $bmap = [];
        $pmap = [];

        foreach (array_keys($ps) as $key) {
            $bmap[] = [$key, true];
            $pmap[] = [$key, ${$key}];
        }

        $rep = $this->mockRepository();
        $rep->method('all')->willReturn([$p1]);
        $rep->method('has')->will($this->returnValueMap($bmap));
        $rep->method('get')->will($this->returnValueMap($pmap));

        try {
            $dep->getSorted($rep);
        } catch (RequirementException $e) {
            $this->assertEquals(
                'Circular reference error: Provider "foo" requires "p1" which requires "foo".',
                $e->getMessage()
            );
        }
    }
    /** @test */
    public function itShouldExplodeOnMissingProvider()
    {
        $dep = new Dependency;
        $ps = [
            'p1' => ['foo']
        ];

        $nn = $this->getProviderMocks($ps);

        extract($nn);

        $bmap = [];
        $pmap = [];

        foreach (array_keys($ps) as $key) {
            $bmap[] = [$key, true];
            $pmap[] = [$key, ${$key}];
        }
        $bmap[] = ['foo', false];

        $rep = $this->mockRepository();
        $rep->method('all')->willReturn([$p1]);
        $rep->method('has')->will($this->returnValueMap($bmap));
        $rep->method('get')->will($this->returnValueMap($pmap));

        try {
            $dep->getSorted($rep);
        } catch (RequirementException $e) {
            $this->assertEquals(
                'Provider "p1" requires provider "foo", but provider "foo" doesn\'t exist.',
                $e->getMessage()
            );
        }
    }

    /** @test */
    public function itShouldGetDependencies()
    {
        $dep = new Dependency;

        $ps = [
            'p10' => [],
            'p1'  => ['p10'],
            'p2'  => ['p1', 'p4'],
            'p3'  => ['p1'],
            'p4'  => ['p1', 'p5'],
            'p5'  => ['p1', 'p3', 'optional?']
        ];

        $nn = $this->getProviderMocks($ps);

        extract($nn);

        $bmap = [];
        $pmap = [];

        foreach (array_keys($ps) as $key) {
            $bmap[] = [$key, true];
            $pmap[] = [$key, ${$key}];
        }

        $rep = $this->mockRepository();
        $rep->method('all')->willReturn([$p4, $p5, $p3, $p2, $p1, $p10]);
        $rep->method('has')->will($this->returnValueMap($bmap));
        $rep->method('get')->will($this->returnValueMap($pmap));

        $this->assertEquals(['p10', 'p1', 'p3', 'p5', 'p4', 'p2'], array_keys($dep->getSorted($rep)));
        $this->assertEquals(['p10'], array_keys($dep->getRequirements($p1, $rep)));
        $this->assertEquals(['p10', 'p1'], array_keys($dep->getRequirements($p1, $rep, true)));
        $this->assertEquals(['p10', 'p1', 'p3', 'p5', 'p4'], array_keys($dep->getRequirements($p2, $rep)));
    }

    private function mockProvider($alias, $name, $r = [])
    {
        $mock = $this->getMock('Lucid\Package\AbstractProvider');
        $mock->method('getName')->willReturn($name);
        $mock->method('getAlias')->willReturn($alias);
        $mock->method('requires')->willReturn($r);

        return $mock;
    }

    private function mockRepository($methods = [])
    {
        return $this->getMock('Lucid\Package\AbstractRepository', []);
    }

    private function getProviderMocks(array $defs)
    {
        $nn = [];

        foreach ($defs as $p => $n) {
            $nn[$p] = $this->mockProvider($p, $p.'Provider', $n);
        }

        return $nn;
    }
}
