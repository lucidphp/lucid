<?php

/*
 * This File is part of the Lucid\DI\Tests\Stubs package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests\Stubs;

/**
 * @class StaticFactory
 *
 * @package Lucid\DI\Tests\Stubs
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StaticFactory
{
    public static $testCase;

    public static function makeA()
    {
        return new \stdClass;
    }

    public static function makeB($a, $b)
    {
        $mock = self::$testCase->getMock(__NAMESPACE__.'\MyService', ['getA', 'getB']);
        $mock->method('getA')->willReturn($a);
        $mock->method('getB')->willReturn($b);

        return $mock;
    }
}
