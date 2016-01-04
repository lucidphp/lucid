<?php

/*
 * This File is part of the Lucid\Package\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package\Exception;

use LogicException;

/**
 * @class RequirementException
 *
 * @package Lucid\Package\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequirementException extends LogicException
{
    /**
     * missingPackage
     *
     * @param string $parent
     * @param string $missing
     * @param string $prefix
     *
     * @return self
     */
    public static function missingPackage($parent, $missing, $prefix = 'Provider')
    {
        return new self(
            sprintf(
                '%s "%3$s" requires %2$s "%4$s", but %2$s "%4$s" doesn\'t exist.',
                ucfirst($prefix),
                strtolower($prefix),
                $parent,
                $missing
            )
        );
    }
    /**
     * circularReference
     *
     * @param string $parent
     * @param string $conflict
     * @param string $prefix
     *
     * @return self
     */
    public static function circularReference($parent, $conflict, $prefix = 'Provider')
    {
        return new self(
            sprintf(
                'Circular reference error: %3$s "%1$s" requires "%2$s" which requires "%1$s".',
                $parent,
                $conflict,
                $prefix
            )
        );
    }
}
