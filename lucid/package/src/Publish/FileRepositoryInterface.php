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

interface FileRepositoryInterface
{
    /** @var bool */
    const FILE_OVERRIDE    = true;

    /** @var bool */
    const FILE_NO_OVERRIDE = false;

    /**
     * Sets the files array.
     *
     * @param array $files `FileTargetInterface[]`
     *
     * @return void
     */
    public function setFiles(array $files);

    /**
     * Creates a new instance of FileTargetInterface.
     *
     * @param string $file
     * @param string $relPath
     *
     * @return FileTargetInterface
     */
    public function createTarget($file, $relPath = null);

    /**
     * Adds a target to the files pool.
     *
     * @param FileTargetInterface $target
     *
     * @return void
     */
    public function addFile(FileTargetInterface $target);

    /**
     * Returns all files as array.
     *
     * @return array
     */
    public function getFiles();

    /**
     * Writes all target files to a target path.
     *
     * @param string  $targetPath
     * @param bool $override
     *
     * @return void
     */
    public function dumpFiles($targetPath, $override = self::FILE_NO_OVERRIDE);

    /**
     * Writes a target file to a target path.
     *
     * @param FileTargetInterface $file
     * @param string $targetPath
     * @param bool $override
     *
     * @return void
     */
    public function dumpFile(FileTargetInterface $file, $targetPath, $override = self::FILE_NO_OVERRIDE);

    /**
     * Returns the target path from a file target.
     *
     * @param FileTargetInterface $file
     * @param string $targetPath
     *
     * @return string
     */
    public function getTargetPath(FileTargetInterface $file, $targetPath);
}
