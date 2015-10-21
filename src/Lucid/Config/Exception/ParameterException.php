<?php

/*
 * This File is part of the Lucid\Config package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Config\Exception;

/**
 * @class ParameterException
 * @see \Exception
 *
 * @package Lucid\Config\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ParameterException extends \Exception
{
    public static function lockedCall($method)
    {
        return new static(sprintf('Cannot call %s() on static parameters', $method));
    }

    public static function undefinedParameter($param)
    {
        return new static(sprintf('Parameter "%s" is not defined.', $param));
    }

    public static function nonScalarValues($param)
    {
        return new static(sprintf('String "%s" contains non scalar values.', $param));
    }

    public static function circularReference($param)
    {
        return new static(sprintf('Parameter variable "%s" is referencing itself.', $param));
    }
}
