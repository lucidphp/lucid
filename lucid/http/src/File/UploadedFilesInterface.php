<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\File;

use Psr\Http\Message\UploadedFileInterface;

/**
 * @interface UploadedFilesInterface
 *
 * @package Lucid\Http
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
     *
     * @param string $path
     * @param boolean $fileAsObj
     *
     * @return \Psr\http\Message\UploadedFileInterface
     */
    public function get($filePath);

    /**
     * Get the fixed files array
     *
     * This method should return an array containing all uploaded files as File
     * objects. File Objects must implement the
     * Psr\Http\Message\UploadedFileInterface.
     *
     * @return array an array containing instances of Psr\Http\Message\UploadedFileInterface
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
