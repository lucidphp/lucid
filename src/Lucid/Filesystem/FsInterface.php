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

/**
 * @interface FsInterface
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface FsInterface
{
    /**
     * Tell if a path exists.
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
     * Check if path is a symbolic link.
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isLink($path);

    /**
     * Copy a path to a new location
     *
     * @param string $path
     * @param string $target
     *
     * @throws IOException
     *
     * @return boolean
     */
    public function copy($path, $target);

    /**
     * Moves a path to a new location
     *
     * @param string $path
     * @param string $target
     *
     * @throws IOException
     *
     * @return boolean
     */
    public function rename($path, $target);

    /**
     * Removes a directory or file
     *
     * @param string $path
     *
     * @throws IOException
     *
     * @return boolean
     */
    public function remove($path);

    /**
     * Write content to a file.
     *
     * @return int Bytes written
     */
    public function putFile($path, $content = null);

    /**
     * Get a files content.
     *
     * @param string $path
     * @param int $offset
     * @param int $maxlen
     *
     * @throws IOException
     *
     * @return string
     */
    public function dumpFile($path, $offset = 0, $maxlen = -1);

    /**
     * Writes contents of a stream to a given file.
     *
     * @param string $path
     * @param resource $stream
     * @param int $offset
     * @param int $maxlen
     *
     * @throws IOException
     *
     * @return void
     */
    public function putStream($path, $stream, $offset = 0, $maxlen = -1);

    /**
     * readStream
     *
     * @param mixed $path
     * @param mixed $stream
     * @param int $offset
     * @param mixed $maxlen
     *
     * @throws IOException
     *
     * @return resource
     */
    public function getStream($path, $offset = 0, $maxlen = -1);

    /**
     * Creates a new Directory
     *
     * @param mixed $path
     * @param mixed $permission
     *
     * @return void
     */
    public function mkdir($path, $permission = null, $recursive = false);

    /**
     * Set permission on a given path.
     *
     * @param string $path
     * @param int $permission
     * @param boolean $recursive
     *
     * @return boolean
     */
    public function chmod($path, $mode = null, $recursive = false);

    /**
     * Set user group on a given path.
     *
     * @param string $path
     * @param int $group
     * @param boolean $recursive
     *
     * @return boolean
     */
    public function chgrp($path, $group = null, $recursive = false);

    /**
     * Set owner on a given path.
     *
     * @param string $path
     * @param int $owner
     * @param boolean $recursive
     *
     * @return boolean
     */
    public function chown($path, $group = null, $recursive = false);

    /**
     * Set visibility on a given path.
     *
     * @param string $path
     * @param string $visibility
     * @param boolean $recursive
     *
     * @return boolean
     */
    public function setVisibility($path, $visibility = null, $recursive = false);

    /**
     * Get the file permission and visibility of a given path
     *
     * @param string $path
     *
     * @return PermissionInterface
     */
    public function getPermission($path, $visibility = null, $recursive = false);

    /**
     * Get the file permission and visibility of a given path
     *
     * @param string $path
     *
     * @return PermissionInterface
     */
    public function filePerms($path);

    /**
     * Lists files and directories of a given path.
     *
     * @param string $path
     * @param boolean $recursive
     *
     * @return array
     */
    public function listDirectory($path, $recursive = false);

    /**
     * Get a pathinfo object from a given path.
     *
     * @param string $path
     *
     * @return PathInfo
     */
    public function getPathInfo($path);
}
