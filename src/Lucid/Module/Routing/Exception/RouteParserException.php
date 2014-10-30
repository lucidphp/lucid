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
 * @class RouteParserException
 *
 * @package Lucid\Module\Routing\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteParserException extends \LogicException
{
    public static function nestedOptional($param)
    {
        return new self(sprintf('Nested optional parameter {%s} has no default value.', $param));
    }
}
