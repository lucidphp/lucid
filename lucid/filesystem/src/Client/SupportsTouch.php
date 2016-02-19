<?php

/*
 * This File is part of the Lucid\Filesystem\Client package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Client;

/**
 * @interface NixLike
 *
 * @package Lucid\Filesystem\Client
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SupportsTouch
{
    public function touch($path, $time, $atime);
}
