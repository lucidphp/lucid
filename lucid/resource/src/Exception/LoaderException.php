<?php

/*
 * This File is part of the Lucid\Resource\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Exception;

/**
 * @class LoaderException
 *
 * @package Lucid\Resource\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LoaderException extends \Exception
{
    /**
     * Returns a LoaderException instance with predefined error message.
     *
     * @param mixed $resource
     *
     * @return LoaderException
     */
    public static function missingLoader($resource)
    {
        return new self(
            sprintf('No loader found for resource "%s".', is_string($resource) ? $resource : gettype($resource))
        );
    }
}
