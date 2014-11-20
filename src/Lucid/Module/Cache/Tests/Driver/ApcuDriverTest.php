<?php

/*
 * This File is part of the Lucid\Module\Cache\Tests\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Cache\Tests\Driver;

use Lucid\Module\Cache\Driver\ApcuDriver;

/**
 * @class ApcDriverTest
 *
 * @package Lucid\Module\Cache\Tests\Driver
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
