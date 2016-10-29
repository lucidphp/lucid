<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Tests\Handler\Stubs package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Tests\Handler\Stubs;

/**
 * @class Handler
 *
 * @package Lucid\Mux\Tests\Handler\Stubs
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SimpleHandler
{
    public function noneParamAction()
    {
        return true;
    }
}
