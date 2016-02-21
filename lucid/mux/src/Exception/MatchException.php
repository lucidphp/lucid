<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Exception;

/**
 * @class MatchException
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MatchException extends \RuntimeException
{
    public static function noRouteMatch($request)
    {
        return new self(sprintf('No route found for requested resource "%s".', $request->getPath()));
    }
}
