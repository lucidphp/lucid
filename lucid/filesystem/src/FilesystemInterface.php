<?php

/*
 * This File is part of the Lucid\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem;

use Lucid\Filesystem\Exception\IOException;

/**
 * @interface FilesystemInterface
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface FilesystemInterface
{
    /**
     * @var string
     */
    const PERM_PUBLIC =  'public';

    /**
     * @var string
     */
    const PERM_PRIVATE = 'private';

    /**
     * @var string
     */
    const COPY_PREFIX = 'copy';

    /**
     * @var int
     */
    const COPY_START_OFFSET = 1;

    public function getPathInfo($path);

    /**
     * Checks if a path exists.
     *
     * @param string $path
     *
     * @return boolean
     */
    public function exists($path);

    /**
     * Check if path is a directory.
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isDir($file);

    /**
     * Check if path is a file.
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isFile($path);

    /**
     * Check if path is a link.
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isLink($path);

    /**
     * Ensure that a directory exists.
     *
     * Will create the dircetory if needed.
     *
     * @param string $file
     *
     * @return void
     */
    public function ensureDirectory($dir);

    /**
     * Ensure that a file exists.
     *
     * Will create the file if needed.
     *
     * @param string $file
     *
     * @return void
     */
    public function ensureFile($file);

    /**
     * Write content to a file.
     *
     * @param string $file
     * @param string $content
     *
     * @return boolean
     */
    public function writeFile($file, $content);

    public function updateFile($file, $content);

    /**
     * Dumps the contents of a file.
     *
     * @param string   $file
     * @param int|null $start
     * @param int|null $stop
     *
     * @return string
     */
    public function readFile($file, $offset = null, $maxlen = null);

    /**
     * Writes a stream resource to a given file path.
     *
     * @param string   $file
     * @param resource $stream
     *
     * @return array
     */
    public function writeStream($file, $stream, $offset = null, $maxlen = null);

    /**
     * updateStream
     *
     * @param mixed $file
     * @param mixed $stream
     * @param mixed $offset
     * @param mixed $maxlen
     *
     * @return void
     */
    public function updateStream($file, $stream, $offset = null, $maxlen = null);

    /**
     * Get a filestream from a given file path.
     *
     * @param string $file
     *
     * @return resource
     */
    public function readStream($file);

    /**
     * Enumrate a given path name.
     *
     * @param string  $file
     * @param int     $start
     * @param string  $prefix
     * @param boolean $pad
     *
     * @return string
     */
    public function enum($path, $prefix = null, $pad = true);

    /**
     * Creates a copy of a given resrouce.
     *
     * The path name will be created in the form
     * of basepath/<suffix><basename><date>[<enum>.<extension>]
     *
     * @param string $path the path to copy
     * @param string $dateFormat a valid dat format
     * @param string $suffix backup name prefix
     *
     * @return array
     */
    public function backup($path, $dateFormat = 'Y-m-d-His', $suffix = '~');

    /**
     * Creates a directory.
     *
     * @param string  $dir the path to the directory to be Created.
     * @param int     $mod the permission level expressed as a 4 digit octagonal
     * mask
     * @param boolean $recursive
     *
     * @return boolean
     */
    public function mkdir($dir, $mod = 0755, $recursive = true);

    /**
     * Removes a directory entirely.
     *
     * @param string $directory source path ot hthe directory to be removed.
     *
     * @throws IOException
     *
     * @return boolean Always `TRUE`
     */
    public function rmdir($directory);

    /**
     * Removes all conatining files and directories within the given directory.
     *
     * Flushin a directory will keep the directory itself, but removes all
     * containing items.
     *
     * @param string $directory source path ot hthe directory to be flushed.
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return void
     */
    public function flush($directory);

    /**
     * Touches a file.
     *
     * Will update a file's temestamp or create a new file if the given file
     * doesn't exists.
     *
     * Depending on the Filesystem driver, $atime may not be affective.
     *
     * @param string  $file  the file path.
     * @param integer $time  the modifytime as unix timestamp.
     * @param integer $atime the accesstime as unix timestamp.
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return boolean true if touching of the file was successful.
     */
    public function touch($file, $time = null, $atime = null);

    /**
     * Deletes a file.
     *
     * @param string $file the file path.
     *
     * @return boolean
     */
    public function unlink($file);

    /**
     * Moves a path to a new name.
     *
     * @param string  $source the source path
     * @param string  $target the new path
     *
     * @return array|false
     */
    public function rename($source, $target);

    /**
     * Removes files or directories.
     *
     * @param string $file Path that should be removed.
     *
     * @throws IOException
     *
     * @return boolean Always `TRUE`
     */
    public function remove($file);

    /**
     * Copies a file or directory to a target destination.
     *
     * If $target is null, copy() will copy the file or directory to its
     * parent directory and will enumerate its name.
     *
     * @param string  $source
     * @param string  $target
     *
     * @throws IOException
     *
     * @return integer the total count of bytes that where copied.
     */
    public function copy($source, $target = null);

    /**
     * Set file permissions.
     *
     * @param string  $file
     * @param integer $permission
     * @param boolean $recursive
     *
     * @throws IOException if permissions
     * could not be set.
     *
     * @return boolean true
     */
    public function chmod($file, $permission = 0755, $recursive = true, $umask = 0000);

    /**
     * Change file ownership.
     *
     * Depending on the Filesystem driver, this may not be available.
     *
     * @param string     $file
     * @param string|int $owner
     * @param boolean    $recursive
     *
     * @throws IOException if UID doesn't
     * exist.
     * @throws IOException if ownership
     * could not be changed.
     * @return boolean true
     */
    public function chown($file, $owner, $recursive = true);

    /**
     * Change file group.
     *
     * Depending on the Filesystem driver, this may not be available.
     *
     * @param string     $file
     * @param string|int $group
     * @param boolean    $recursive
     *
     * @throws IOException if GID doesn't
     * exist.
     * @throws IOException if group
     * could not be changed.
     *
     * @return boolean true
     */
    public function chgrp($file, $group, $recursive = true);
}
