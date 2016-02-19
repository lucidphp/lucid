<?php

/*
 * This File is part of the Lucid\Package package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package\Publish;

/**
 * @interface FileTargetInterface
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface FileTargetInterface
{
    /**
     * Returns the target file name.
     *
     * @return string
     */
    public function getFilename();

    /**
     * Returns the file contents.
     *
     * @return string
     */
    public function getContents();

    /**
     * Returns relative path of the target file.
     *
     * @return string
     */
    public function getRelativePath();

    /**
     * isValid
     *
     * @return bool
     */
    public function isValid();
}
