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
     * Get the error code.
     *
     * @return int
     */
    public function getErrorCode();
}
