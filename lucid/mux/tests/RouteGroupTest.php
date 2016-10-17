<?php
/**
 * Created by PhpStorm.
 * User: malcolm
 * Date: 17.10.16
 * Time: 20:01
 */

namespace Lucid\Mux\Tests;


use Lucid\Mux\RouteGroup;


class RouteGroupTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstatiable()
    {
        $this->assertInstanceOf(RouteGroup::class, new RouteGroup('foo', []));
    }

    /** @test */
    public function itShouldTrimPrefixes()
    {
        $group  = new RouteGroup('group/', []);
        $this->assertSame('/group', $group->getPrefix());

    }

    /** @test */
    public function itShouldHasParent()
    {
        $parent  = new RouteGroup('parent/', []);
        $this->assertFalse($parent->hasParent());

        $child  = new RouteGroup('child/', [], $parent);
        $this->assertTrue($child->hasParent());
    }

    /** @test */
    public function itShouldInheritParentPrefix()
    {
        $parent = new RouteGroup('/parent', []);
        $child  = new RouteGroup('/child', [], $parent);

        $this->assertSame('/parent/child', $child->getPrefix());
    }

    /** @test */
    public function itShouldInheritParentRequirements()
    {
        $parent = new RouteGroup('/parent', $req = ['schemes' => ['https'], 'host' => 'localhost']);
        $child  = new RouteGroup('/child', ['host' => 'example.com'], $parent);
        $this->assertSame(['schemes' => ['https'], 'host' => 'example.com'], $child->getRequirements());
    }

    /** @test */
    public function prefixMustNotBeEmpry()
    {
        try {
            new RouteGroup('', []);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Group prefix may not be empty.', $e->getMessage());
        }
    }
}
