<?php

/*
 * This File is part of the Lucid\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Request;

/**
 * @interface UploadedFilesInterface
 *
 * @package Lucid\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface UploadedFilesInterface
{
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
     * @return UploadedFileInterface
     */
    public function get($getClientFilename);

    /**
     * setFiles
     *
     * @param array $files
     *
     * @return void
     */
    public function setFiles(array $files);

    /**
     * add
     *
     * @param UploadedFileInterface $file
     *
     * @return void
     */
    public function add(UploadedFileInterface $file);

    /**
     * Get the fixed files array
     *
     * This method should return an array containing all uploaded files as File
     * objects. File Objects must implement the
     * Psr\Http\Message\UploadedFileInterface.
     *
     * @return array[Psr\Http\Message\UploadedFileInterface]
     */
    public function all();

    /**
     * Get the raw files input array.
     *
     * This should typically return the files array represented in $__FILES.
     *
     * @return array
     */
    public function raw();
}
