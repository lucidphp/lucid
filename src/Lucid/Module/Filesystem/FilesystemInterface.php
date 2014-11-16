<?php

/*
 * This File is part of the Lucid\Module\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem;

/**
 * @interface FilesystemInterface
 *
 * @package Lucid\Module\Filesystem
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

    /**
     * exists
     *
     * @param mixed $param
     *
     * @return boolean
     */
    public function exists($file);

    /**
     * isDir
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isDir($file);

    /**
     * isFile
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isFile($file);

    /**
     * isLink
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isLink($file);

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
     * setContents
     *
     * @param mixed $file
     * @param mixed $content
     * @param mixed $writeFlags
     *
     * @return boolean
     */
    public function setContents($file, $content);

    /**
     * getContents
     *
     * @param mixed $file
     * @param mixed $includepath
     * @param mixed $context
     * @param mixed $start
     * @param mixed $stop
     *
     * @return string
     */
    public function getContents($file, $start = null, $stop = null);
    //public function getContents($file, $includepath = null, $context = null, $start = null, $stop = null);

    /**
     * enum
     *
     * @param mixed $file
     * @param int $start
     * @param mixed $prefix
     * @param mixed $pad
     *
     * @return string
     */
    public function enum($path, $start = 0, $prefix = null, $pad = true);

    /**
     * backup
     *
     * @param mixed $file
     * @param string $dateFormat
     * @param string $suffix
     *
     * @return void
     */
    public function backup($file, $dateFormat = 'Y-m-d-His', $suffix = '~');

    /**
     * Creates a directory.
     *
     * @param string  $dir the path to the directory to be Created.
     * @param int     $pmask the permission level expressed as a 4 digit hex
     * mask
     * @param boolean $recursive
     *
     * @return boolean
     */
    public function mkdir($dir, $pmask = 0755, $recursive = true);

    /**
     * Removes a directory entirely.
     *
     * @param string $directory source path ot hthe directory to be removed.
     *
     * @return boolean
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
     * Will update ad file's  temestamp or create a new file if the given file
     * doesn't exists.
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
     * rename
     *
     * @param mixed $source
     * @param mixed $target
     * @param mixed $overwrite
     *
     * @return void
     */
    public function rename($source, $target, $overwrite = false);

    /**
     * Removes files or directories.
     *
     * @param string|array $file source paths that should be removed.
     *
     * @return boolean returns true if all files where deleted successfully,
     * and false if some or one file coulnd not be deleted.
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
     * @param boolean $replace
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return integer returns the total count of bytes that where copied.
     */
    public function copy($source, $target = null, $replace = false);

    /**
     * Set file permissions.
     *
     * @param string  $file
     * @param integer $permission
     * @param boolean $recursive
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException if permissions
     * could not be set.
     *
     * @return boolean true
     */
    public function chmod($file, $permission = 0755, $recursive = true, $umask = 0000);

    /**
     * Change file ownership.
     *
     * @param string     $file
     * @param string|int $owner
     * @param boolean    $recursive
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException if UID doesn't
     * exist.
     * @throws \Selene\Module\Filesystem\Exception\IOException if ownership
     * could not be changed.
     * @return boolean true
     */
    public function chown($file, $owner, $recursive = true);

    /**
     * Change file group.
     *
     * @param string     $file
     * @param string|int $group
     * @param boolean    $recursive
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException if GID doesn't
     * exist.
     * @throws \Selene\Module\Filesystem\Exception\IOException if group
     * could not be changed.
     *
     * @return boolean true
     */
    public function chgrp($file, $group, $recursive = true);
}
