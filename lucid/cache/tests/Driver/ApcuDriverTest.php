<?php

/*
 * This File is part of the Lucid\Cache\Tests\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Tests\Driver;

use Lucid\Cache\Driver\ApcuDriver;

/**
 * @class ApcDriverTest
 *
 * @package Lucid\Cache\Tests\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ApcuDriverTest extends ApcDriverTest
{
    public function timeProvider()
    {
        return [
            [60]
        ];
    }

    protected function newDriver()
    {
        return new ApcuDriver;
    }

    protected function setUp()
    {
        include_once dirname(__DIR__).'/Fixures/helper.php';
        include_once dirname(__DIR__).'/Fixures/apcuhelper.php';
    }
}
