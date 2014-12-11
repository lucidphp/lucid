<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session;

use Lucid\Module\Common\Helper\Str;

/**
 * @trait SessionHelperTrait
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait SessionHelperTrait
{
    protected function generateId()
    {
        return sha1(uniqid('', true).Str::rand(25).microtime(true));
    }
}
