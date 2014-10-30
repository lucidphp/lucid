<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests;

use Lucid\Module\Routing\Route;
use Lucid\Module\Routing\RouteParser as Parser;

/**
 * @class RouteParserTest
 *
 * @package Lucid\Module\Routing\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteParserTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $route = new Route('/foo/{user?}/bin', 'action', 'GET', null, ['user' => 'lenny']);

        $r = Parser::parse($route);

        //var_dump($r);
    }
}
