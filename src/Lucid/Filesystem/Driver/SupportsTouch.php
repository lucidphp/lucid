<?php

/*
 * This File is part of the Lucid\Filesystem\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Driver;

/**
 * @interface NixLike
 *
 * @package Lucid\Filesystem\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SupportsTouch
{
    public function touch($path, $time, $atime);
}
