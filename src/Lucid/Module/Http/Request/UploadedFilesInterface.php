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
 * @interface UploadedFilesInterface
 *
 * @package Lucid\Module\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface UploadedFilesInterface
{
    /**
     * Sets the files array.
     *
     * The files array is typically a super global like $_FILES
     *
     * @param array $files
     */
    public function setFilesArray(array $files);

    /**
     * Get the raw files array.
     *
     * @return array
     */
    public function getFilesArray();

    /**
     * Get a file or multiple files by their path.
     *
     * This should return an array of
     * files or a single file, if `$path` points to one file in particular.
     * Use `$fileAsObj` to get files as instance of UploadedFileInterface
     * instead of an array.
     *
     * @param string $path
     * @param boolean $fileAsObj
     *
     * @return array|UploadedFileInterface
     */
    public function get($path, $fileAsObj = false);

    /**
     * Get the fixed files array
     *
     * @return array
     */
    public function all();
}
