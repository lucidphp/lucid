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

use Interop\Container\Exception\NotFoundException as InteropNotFoundException;

/**
 * @class NotFoundException
 *
 * @package Lucid\DI\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class NotFoundException extends \Exception implements InteropNotFoundException
{

    /**
     * Should be uses if a resolving service id is not defined or bound to another
     * service.
     *
     * @param string $id
     *
     * @return NotFoundException
     */
    public static function undefinedService($id)
    {
        return new self(sprintf('The service "%s" is undefined.', $id));
    }

    /**
     * Alias for NotFoundException::undefinedService().
     *
     * @param string $id
     *
     * @return NotFoundException
     */
    public static function serviceUndefined($id)
    {
        return self::undefinedService($id);
    }
}
