<?php

/*
 * This File is part of the Lucid\Filesystem\Helper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Helper;

/**
 * @class PathHelper
 *
 * @package Lucid\Filesystem\Helper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PathHelper
{
    /**
     * normalizePath
     *
     * @param mixed $path
     *
     * @return string
     */
    public static function normalizePath($path)
    {
        if ('.' === $path) {
            $path = '';
        }

        return trim($path, '\\/');
    }

    /**
     * baseName
     *
     * @param string $path
     *
     * @return string
     */
    public static function baseName($path)
    {
        return basename($path);
    }

    /**
     * baseName
     *
     * @param string $path
     *
     * @return string
     */
    public static function dirName($path)
    {
        return static::normalizePath(dirname($path));
    }

    /**
     * contentSize
     *
     * @param string $content
     *
     * @return int
     */
    public static function contentSize($content = null)
    {
        return $content ? mb_strlen($content, '8bit') : 0;
    }

    /**
     * inPath
     *
     * @param string $root
     * @param string $path
     *
     * @return boolean
     */
    public static function inPath($root, $path)
    {
        if ('' === $root) {
            return true;
        }

        if (0 !== $pos = mb_strpos($path, $root, 0, '8bit')) {
            return false;
        }

        $len = mb_strlen($root, '8bit');

        if ('' === ($sp = mb_substr($path, $len, 1, '8bit')) || $this->driver->getSeparator() === $sp) {
            return true;
        }

        return false;
    }
}
