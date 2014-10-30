<?php

/*
 * This File is part of the Lucid\Module\Routing\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Exception;

/**
 * @class MatchException
 *
 * @package Lucid\Module\Routing\Exception
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
