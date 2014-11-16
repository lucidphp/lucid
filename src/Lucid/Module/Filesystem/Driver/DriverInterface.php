<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Driver;

use Lucid\Module\Filesystem\FilesystemInterface as Fs;

/**
 * @interface DriverInterface
 *
 * @package Lucid\Module\Filesystem\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface DriverInterface
{
    /**
     * exists
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function exists($file);

    /**
     * isFile
     *
     * @param mixed $dir
     *
     * @return boolean
     */
    public function isFile($dir);

    /**
     * isDir
     *
     * @param mixed $dir
     *
     * @return boolean
     */
    public function isDir($dir);

    /**
     * createFile
     *
     * @param mixed $file
     * @param mixed $contents
     * @param mixed $permission
     *
     * @return void
     */
    public function writeFile($file, $contents = null);

    /**
     * writeStream
     *
     * @param string $path
     * @param resource $stream
     *
     * @return void
     */
    public function writeStream($path, $stream);

    /**
     * createDirectory
     *
     * @param mixed $dir
     * @param mixed $recursive
     * @param mixed $permission
     *
     * @return void
     */
    public function createDirectory($dir, $permission = 0755, $recursive = true);

    /**
     * deleteFile
     *
     * @param mixed $path
     *
     * @return void
     */
    public function deleteFile($path);

    /**
     * deleteDirectory
     *
     * @param mixed $path
     *
     * @return void
     */
    public function deleteDirectory($path);

    /**
     * rename a file or directory.
     *
     * @param string $source
     * @param string $target
     *
     * @return void
     */
    public function rename($source, $target);

    /**
     * copyFile
     *
     * @param mixed $file
     * @param mixed $target
     *
     * @return void
     */
    public function copyFile($file, $target);

    /**
     * copyDirectory
     *
     * @param mixed $dir
     * @param mixed $target
     *
     * @return void
     */
    public function copyDirectory($dir, $target);

    /**
     * setPermission
     *
     * @param mixed $path
     * @param mixed $permission
     *
     * @return void
     */
    public function setPermission($path, $mod, $recursive = true);
}
