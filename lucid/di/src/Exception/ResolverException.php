<?php

/*
 * This File is part of the Lucid\DI\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Exception;

/**
 * @class ResolverException
 *
 * @package Lucid\DI\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ResolverException extends \RuntimeException
{
    /**
     * Should be used if a service definition is not resolveable.
     *
     * @param string $id
     *
     * @return ResolverException
     */
    public static function notResolveable($id)
    {
        return new self(
            sprintf('The requested service "%s" is not resolveable.', $id)
        );
    }

    public static function notInstantiable($id)
    {
        return new self(
            sprintf('The requested service "%s" is not instantiable.', $id)
        );
    }

    /**
     * Should be used if a service definition is referencing itself.
     *
     * @param string $id
     *
     * @return ResolverException
     */
    public static function circularReference($id)
    {
        return new self(
            sprintf('The requested service "%s" has a circular reference.', $id)
        );
    }

    public static function boundService($id)
    {
        return new self(
            sprintf(
                'The service "%s" is undefined or bound to another service and cannot be resolved on it\'s own.',
                $id
            )
        );
    }
}
