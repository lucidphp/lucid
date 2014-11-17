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
     * pathInfoAsObject
     *
     * @param boolean $obj
     *
     * @return void
     */
    public function pathInfoAsObject($obj);

    /**
     * Tells if given path exists.
     *
     * @param string $path the path
     *
     * @return boolean
     */
    public function exists($path);

    /**
     * Tells if given path is a file.
     *
     * @param string $path the path.
     *
     * @return boolean
     */
    public function isFile($path);

    /**
     * Tells if given path is a symbolic link.
     *
     * @param string $path the path.
     *
     * @return boolean
     */
    public function isLink($path);

    /**
     * Tells if given path is a directory.
     *
     * @param string $path the path.
     *
     * @return boolean
     */
    public function isDir($path);

    /**
     * Writes to a file.
     *
     * @param string      $file     the file path to write to
     * @param string|null $contents the file contents.
     *
     * @return void
     */
    public function writeFile($file, $contents = null);

    /**
     * Updates a file
     *
     * @param string      $file     the file path to write to
     * @param string|null $contents the file contents.
     *
     * @return void
     */
    public function updateFile($file, $contents = null);

    /**
     * Writes a file stream to a path.
     *
     * @param string   $path the target path
     * @param resource $stream the file stream to read from.
     *
     * @return void
     */
    public function writeStream($path, $stream);

    /**
     * Updates a file from a file stream.
     *
     * @param string   $path the target path
     * @param resource $stream the file stream to read from.
     *
     * @return void
     */
    public function updateStream($path, $stream);

    /**
     * Creates a new directory.
     *
     * @param string  $dir the directory path
     * @param int     $permission the permission mode.
     * @param boolean $recursive recursively create path.
     *
     * @return void
     */
    public function createDirectory($dir, $permission = 0755, $recursive = true);

    /**
     * Deletes a file
     *
     * @param string $file the file to delete.
     *
     * @return void
     */
    public function deleteFile($file);

    /**
     * Deletes a directory an its contents.
     *
     * @param string $dir the directory to delete.
     *
     * @return void
     */
    public function deleteDirectory($dir);

    /**
     * Renames a file or directory.
     *
     * @param string $source the source path
     * @param string $target the new path
     *
     * @return array|boolean pathinfo of the target path, false on failure.
     */
    public function rename($source, $target);

    /**
     * Copies a file.
     *
     * @param string $file the source file.
     * @param string $target the targer file path.
     *
     * @return array|boolean pathinfo of the target path, false on failure.
     */
    public function copyFile($file, $target);

    /**
     * Copies a directory.
     *
     * @param string $dir the source directory.
     * @param string $target the targer directory path.
     *
     * @return array|boolean pathinfo of the target path, false on failure.
     */
    public function copyDirectory($dir, $target);

    /**
     * Set file permissions.
     *
     * @param string  $path the file path
     * @param int     $permission permission mode
     * @param boolean $recursive  sets permission recursively if path is
     * directory.
     *
     * @return array|boolean
     */
    public function setPermission($path, $mod, $recursive = true);

    /**
     * Get file permissions.
     *
     * @param string $path
     *
     * @return array|boolean
     */
    public function getPermission($path);

    /**
     * Get information about a given path.
     *
     * @param string $path the path.
     *
     * @return array
     */
    public function getPathInfo($path);

    /**
     * Get the mime type for a given file.
     *
     * @param string $file
     *
     * @return array
     */
    public function getMimeType($file);
}
