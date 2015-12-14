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

use Exception;
use Interop\Container\Exception\ContainerException as InteropContainerException;

/**
 * @class ContainerException
 *
 * @package Lucid\DI\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerException extends Exception implements InteropContainerException
{
    /**
     * Should be used if a service definition already exists.
     *
     * @param string $id
     *
     * @return ContainerException
     */
    public static function alreadySet($id)
    {
        return new self(
            sprintf('A sevice with id "%s" is already set.', $id)
        );
    }

    /**
     * Should be used if a service definition is not instatiable.
     *
     * @param string $id
     *
     * @return ContainerException
     */
    public static function notInstantiable($id)
    {
        return new self(
            sprintf('The requested serive "%s" is not instantiable.', $id)
        );
    }
}
