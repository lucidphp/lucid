<?php

/*
 * This File is part of the Lucid\Resource\Tests\Subs package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Tests\Stubs;

use Lucid\Resource\Loader\AbstractFileLoader;

/**
 * @class PhpFileLoader
 *
 * @package Lucid\Resource\Tests\Subs
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpFileLoader extends AbstractFileLoader
{
    protected function doLoad($resource)
    {
    }

    protected function getExtensions()
    {
        return ['php'];
    }
}
