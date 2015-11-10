<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http;

use Psr\Http\Message\MessageInterface;

/**
 * @class TestRequest
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TestRequest implements MessageInterface
{
    public function getProtocolVersion()
    {
    }

    public function withNewProtocolVersion()
    {
    }
}
