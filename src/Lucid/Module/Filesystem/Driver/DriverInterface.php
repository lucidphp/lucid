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

use Lucid\Module\Filesystem\PathInfo;
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
    public function pathInfoAsObject($obj = null);

    /**
     * Tells if given path exists.
     *
     * @param string $path the path
     *
     * @return boolean `TRUE` if `$path` exists, otherwise `FALSE`.
     */
    public function exists($path);

    /**
     * Tells if given path is a file.
     *
     * @param string $path the path.
     *
     * @return boolean `TRUE` if `$path` is a file, otherwise `FALSE`.
     */
    public function isFile($path);

    /**
     * Tells if given path is a symbolic link.
     *
     * @param string $path the path.
     *
     * @return boolean `TRUE` if `$path` is a link, otherwise `FALSE`.
     */
    public function isLink($path);

    /**
     * Tells if given path is a directory.
     *
     * @param string $path the path.
     *
     * @return boolean `TRUE` if `$path` is a directory, otherwise `FALSE`.
     */
    public function isDir($path);

    /**
     * Writes a new file.
     *
     * @param string      $file     the file path to write to
     * @param string|null $contents the file contents.
     *
     * @return boolean `TRUE` on success, `FALSE` on failure.
     */
    public function writeFile($file, $contents = null);

    /**
     * Writes to an existing file
     *
     * @param string      $file     the file path to write to
     * @param string|null $contents the file contents.
     *
     * @return boolean `TRUE` on success, `FALSE` on failure.
     */
    public function updateFile($file, $contents = null);

    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @param int $offset
     * @param int $maxlen
     *
     * @return string|boolean The contents of the file, `FALSE` on failure
     */
    public function readFile($path, $offset = null, $maxlen = null);

    /**
     * Writes a file stream to a path.
     *
     * @param string   $path the target path
     * @param resource $stream the file stream to read from.
     *
     * @return int|boolean Bytes writte on success, `FALSE` on failure.
     */
    public function writeStream($path, $stream, $offset = null, $maxlen = null);

    /**
     * Updates a file from a file stream.
     *
     * @param string   $path the target path
     * @param resource $stream the file stream to read from.
     *
     * @return int|boolean Bytes writte on success, `FALSE` on failure.
     */
    public function updateStream($path, $stream, $offset = null, $maxlen = null);

    /**
     * Retuens a stream from a path
     *
     * @param string $path
     *
     * @return resource|boolean Filehandle resource of the given path, `FALSE` on
     * failure
     */
    public function readStream($path);

    /**
     * Creates a new directory.
     *
     * @param string  $dir the directory path
     * @param int     $permission the permission mode.
     * @param boolean $recursive recursively create path.
     *
     * @return boolean `TRUE` on success, `FALSE` on failure.
     */
    public function createDirectory($dir, $permission = 0755, $recursive = true);

    /**
     * Deletes a file
     *
     * @param string $file the file to delete.
     *
     * @return boolean `TRUE` on success, `FALSE` on failure.
     */
    public function deleteFile($file);

    /**
     * Deletes a directory an its contents.
     *
     * @param string $dir the directory to delete.
     *
     * @return boolean `TRUE` on success, `FALSE` on failure.
     */
    public function deleteDirectory($dir);

    /**
     * Renames a file or directory.
     *
     * @param string $source the source path
     * @param string $target the new path
     *
     * @return boolean `TRUE` on success, `FALSE` on failure.
     */
    public function rename($source, $target);

    /**
     * Copies a file.
     *
     * @param string $file the source file.
     * @param string $target the targer file path.
     *
     * @return boolean, `TRUE` on sucess, `FALSE` on failure.
     */
    public function copyFile($file, $target);

    /**
     * Copies a directory.
     *
     * @param string $dir the source directory.
     * @param string $target the targer directory path.
     *
     * @return int|boolean Bytes copied, `FALSE` on failure.
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
     * @return array|boolean Array containing `path`, `$permission`, and `$visibility`, `FALSE` on failure.
     */
    public function setPermission($path, $mod, $recursive = true);

    /**
     * Get path permissions.
     *
     * @param string $path the path
     *
     * @return array|boolean Array containing `path`, `$permission`, and `$visibility`,
     * `FALSE` on failure.
     */
    public function getPermission($path);

    /**
     * Get the mime type for a given file.
     *
     * @param string $file
     *
     * @return string|boolean The mimetype of the file,
     * `FALSE` on failure.
     */
    public function getMimeType($file);

    /**
     * Get information about a given path.
     *
     * @param string $path the path.
     *
     * @return array|PathInfo
     */
    public function getPathInfo($path);

    /**
     * List the contents of a directory
     *
     * @param string $path
     *
     * @return array Array containing PathInfo objects.
     */
    public function listDirectory($path, $recursive = false);

    /**
     * ensureFile
     *
     * @param mixed $path
     *
     * @return void
     */
    public function ensureFile($path);

    /**
     * ensureDirectory
     *
     * @param mixed $path
     *
     * @return void
     */
    public function ensureDirectory($path);
}
