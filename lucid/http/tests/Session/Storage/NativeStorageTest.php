<?php

/*
 * This File is part of the Lucid\Module\Http\Tests\Session\Storage package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests\Session\Storage;

use Lucid\Http\Session\Storage\NativeStorage;

/**
 * @class NativeStorageTest
 *
 * @package Lucid\Module\Http\Tests\Session\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class NativeStorageTest extends StorageTest
{
    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function itShouldThrowExceptionIfSessionWasAlreadyStartedByPhp()
    {
        unset($GLOBALS['sess_stat']);
        $GLOBALS['sess_stat'] = PHP_SESSION_ACTIVE;

        $store = $this->newStore();
        $store->start();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function itShouldThrowExceptionIfSessionCouldNotBeStarted()
    {
        unset($GLOBALS['sess_start_fail']);
        $GLOBALS['sess_start_fail'] = true;

        $store = $this->newStore();
        $store->start();
    }

    protected function newStore($name = 'TESTSESSION')
    {
        return new NativeStorage($name, $this->mockHandler());
    }

    protected function setUp()
    {
        unset($GLOBALS['sess_start_fail']);
        unset($GLOBALS['sess_stat']);
        unset($GLOBALS['sess_name']);
        unset($GLOBALS['sess_id']);
        unset($GLOBALS['session']);
        unset($_SESSION);

        $GLOBALS['sess_stat'] = PHP_SESSION_NONE;
        $GLOBALS['sess_name'] = 'PHPSESSION';
        $GLOBALS['sess_id'] = null;
        $GLOBALS['session'] = [];

        require_once __DIR__.'/nativestoragehelper.php';
    }

    protected function tearDown()
    {
    }
}
