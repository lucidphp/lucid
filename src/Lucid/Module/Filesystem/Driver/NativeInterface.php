<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Driver;

/**
 * @interface NixLike
 *
 * @package Lucid\Module\Filesystem\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface NativeInterface
{
    public function touch($path, $time, $atime);
}
