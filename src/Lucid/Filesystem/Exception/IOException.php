<?php

/*
 * This File is part of the Lucid\Filesystem\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Exception;

/**
 * @class IOException
 *
 * @package Lucid\Filesystem\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IOException extends \RuntimeException
{
    public static function rmDir($dir)
    {
        return new self(sprintf('Could not remove directory "%s".', $dir));
    }

    public static function rmFile($file)
    {
        return new self(sprintf('Could not remove file "%s".', $file));
    }

    public static function createDir($dir)
    {
        return new self(sprintf('Could not create directory "%s".', $dir));
    }

    public static function createFile($file)
    {
        return new self(sprintf('Could not create file "%s".', $file));
    }

    public static function readFile($file)
    {
        return new self(sprintf('Could not read file "%s".', $file));
    }

    public static function writeFile($file)
    {
        return new self(sprintf('Could not write to file "%s".', $file));
    }

    public static function gidError($group)
    {
        return new self(sprintf('Group %s does not exist.', $group));
    }

    public static function uidError($user)
    {
        return new self(sprintf('User %s does not exist.', $user));
    }

    public static function chmodError($file)
    {
        return new self(sprintf('Permissions on %s could not be set.', $file));
    }

    public static function chownError($file, $linkError = false)
    {
        return new self(sprintf('Could not change owner on %s%s.', $linkError ? 'link ' : '', $file));
    }

    public static function chgrpError($file, $linkError = false)
    {
        return new self(sprintf('Could not change group on %s%s.', $linkError ? 'link ' : '', $file));
    }

}
