<?php

/*
 * This File is part of the Lucid\Adapter\Filesystem\Tests\Helper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Filesystem\Tests\Helper;

/**
 * @class FtpHelper
 *
 * @package Lucid\Adapter\Filesystem\Tests\Helper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FtpHelper
{
    public static $files;

    /**
     * files
     *
     *
     * @return void
     */
    public static function files()
    {
        return static::$files;
    }

    /**
     * get
     *
     * @param mixed $path
     *
     * @return void
     */
    public static function get($path)
    {
        return static::$files[$path];
    }

    /**
     * has
     *
     * @param mixed $path
     *
     * @return void
     */
    public static function has($path)
    {
        return isset(static::$files[$path]);
    }

    /**
     * makeNList
     *
     * @param mixed $path
     * @param mixed $info
     *
     * @return void
     */
    public static function makeNList($path, $info = false)
    {
        $out = [];

        foreach (static::files() as $npath => $item) {
            if (0 === strcmp($npath, $path)) {
                $out[] = $info ? $npath : basename($npath);
                continue;
            }

            if ('.' === ($dn = dirname($path))) {
                continue;
            }

            if (0 === strpos($npath, $dn) && 2 < ($c = substr_count($npath, '/'))) {
                $out[] = $info ? $npath : basename($npath);
            }
        }

        return empty($out) ? false : $out;
    }

    /**
     * makeList
     *
     * @param mixed $path
     *
     * @return void
     */
    public static function makeList($path)
    {
        if (!$list = static::makeNlist($path, true)) {
            return false;
        }

        $out = ['STAT START'];

        $info = static::get($path);

        if ('dir' === $info['attributes']['type']) {
            $out[] = 'drwxrw-r--   6 user      group        4096 May 10 4:15  .';
            $out[] = 'drwxrw-r--   6 user      group        4096 May 10 4:15  ..';
        }

        foreach ($list as $path) {
            $out[] = static::statFile($path);
        }

        $out[] = 'STAT END';

        return $out;
    }

    /**
     * statFile
     *
     * @param mixed $path
     *
     * @return void
     */
    public static function statFile($path)
    {
        $ln = '%s   %d user      group        %d May 10 4:15  %s';

        $file  = static::get($path);
        $size  = 'dir' === $file['attributes']['type'] ? 4096 : mb_strlen($file['attributes']['contents']);
        $count = 'dir' === $file['attributes']['type'] ? count(static::makeNlist($path)) : 1;
        $perms = static::translatePem($file['attributes']['perm'], $file['attributes']['type']);

        return sprintf($ln, $perms, $count, $size, basename($path));
    }

    /**
     * translatePem
     *
     * @param mixed $perms
     * @param string $type
     *
     * @return void
     */
    public static function translatePem($perms, $type = 'dir')
    {
        $type = 'dir' === $type ? 'd' : '-';
        $val = $type;

        // Owner; User
        $val .= (($perms & 0x0100) ? 'r' : '-'); //Read
        $val .= (($perms & 0x0080) ? 'w' : '-'); //Write
        $val .= (($perms & 0x0040) ? 'x' : '-'); //Execute

        // Group
        $val .= (($perms & 0x0020) ? 'r' : '-'); //Read
        $val .= (($perms & 0x0010) ? 'w' : '-'); //Write
        $val .= (($perms & 0x0008) ? 'x' : '-'); //Execute

        // Global; World
        $val .= (($perms & 0x0004) ? 'r' : '-'); //Read
        $val .= (($perms & 0x0002) ? 'w' : '-'); //Write
        $val .= (($perms & 0x0001) ? 'x' : '-'); //Execute

        return $val;
    }
}
