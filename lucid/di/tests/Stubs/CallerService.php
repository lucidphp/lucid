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
 * @class CallerService
 *
 * @package Lucid\DI\Tests\Stubs
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CallerService
{
    public $value = 'error';

    public function set($value = 'error')
    {
        $this->value = $value;
    }
}
