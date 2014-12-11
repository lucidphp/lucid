<?php

/*
 * This File is part of the Lucid\Module\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Request;

/**
 * @interface UploadedFileInterface
 *
 * @package Lucid\Module\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface UploadedFileInterface
{
    /**
     * Get the file name as set by the upload form.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the file path of the uploaded file.
     *
     * @return string
     */
    public function getPath();

    /**
     * Get the mimetype of the uploaded file.
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Get the filesize of the uploaded file.
     *
     * @return int
     */
    public function getSize();

    /**
     * Check if the upload was errored.
     *
     * @return boolean
     */
    public function isError();

    /**
     * Move the uploaded file to a new location.
     *
     * @param string $targetPath target directory.
     * @param string $newName    use this name instead of
     *                           `UploadedFileInterface::getName()`
     *
     * @return \SplObject `FALSE` if error.
     */
    public function move($targetPath, $newName = null);

    /**
     * Get the error code.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     *
     * @return int one of the error codes described above.
     */
    public function getErrorCode();
}
