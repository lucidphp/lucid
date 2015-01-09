<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Tests\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Tests\Driver;

use Lucid\Module\Filesystem\Driver\FtpDriver;

/**
 * @class FtpFriverTest
 *
 * @package Lucid\Module\Filesystem\Tests\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class _FtpFriverTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldConnect()
    {
        $options = [
            'host' => 'sedna.thomas-appel.com',
            'user' => 'thomas',
            'password' => 'Bogota97Heerscharen',
            'passive' => true,
            'ssl' => true,
        ];

        $ftp = new FtpDriver($options);

        $cn = $ftp->getConnection();

        //var_dump(ftp_raw($cn, 'STAT foo'));

        //$info = $ftp->getPathInfo('somefile');

        //var_dump($info);


        //$ftp->createDirectory($dir = 'foo/bar/test_'.time(), true, 0775);

        //$info = $ftp->getPathInfo($dir);

        //var_dump($info);
    }

    protected function setUp()
    {
        require_once __DIR__ . '/../Fixures/ftphelper.php';
    }

}
