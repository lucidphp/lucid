<?php

/*
 * This File is part of the Lucid\Module\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Traits;

trait PathHelperTrait
{
    /**
     * dirname
     *
     * @param mixed $dirname
     *
     * @return string
     */
    public function dirname($dirname)
    {
        return dirname($dirname);
    }

    /**
     * isRelativePath
     *
     * @param mixed $file
     *
     * @access public
     * @return bool
     */
    public function isRelativePath($path)
    {
        return !$this->isAbsolutePath($path);
    }

    /**
     * isAbsolutePath
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isAbsolutePath($path)
    {
        return strspn($file, '/\\', 0, 1) || null !== parse_url($file, PHP_URL_SCHEME);
    }

    /**
     * substitutePaths
     *
     * @param string $root
     * @param string $current
     *
     * @return string
     */
    public function substitutePaths($root, $current)
    {
        $path = substr($current, 0, strlen($root));

        if (strcasecmp($root, $path) !== 0) {
            throw new \InvalidArgumentException('Root path does not contain current path');
        }

        $subPath = substr($current, strlen($root) + 1);
        return false === $subPath ? '' : $subPath;
    }

    /**
     * expandPath
     *
     * @param mixed $path
     *
     * @access public
     * @return string
     */
    public function expandPath($path)
    {
        $prefix = $this->isAbsolutePath($path) ? DIRECTORY_SEPARATOR : '';

        $bits = explode(DIRECTORY_SEPARATOR, str_replace('\\/', DIRECTORY_SEPARATOR, $path));

        $p = [];

        $skip = 0;

        while (count($bits)) {

            $part = array_pop($bits);

            if (0 === strcmp($part, '..')) {
                $skip++;
                continue;
            }

            if (0 < $skip) {
                $skip--;
                continue;
            }

            if ('' !== $part) {
                $p[] = $part;
            }
        }

        return $prefix . trim(implode(DIRECTORY_SEPARATOR, array_reverse($p)), DIRECTORY_SEPARATOR);
    }

    /**
     * normalizePath
     *
     * @param string $path
     *
     * @return string
     */
    public function normalizePath($path)
    {
    }
}
