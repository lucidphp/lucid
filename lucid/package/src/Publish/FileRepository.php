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
 * @class FileRepository
 * @see FileRepositoryInterface
 *
 * @package Lucid\Package\Publish
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FileRepository implements FileRepositoryInterface
{
    /** @var array FileTargetInterface[] */
    private $files;

    /**
     * Create a new FileTargetRepository instance.
     *
     * @param array $files `FileTargetInterface[]`
     */
    public function __construct(array $files = [])
    {
        $this->setFiles($files);
    }

    /**
     * {@inheritdoc}
     */
    public function setFiles(array $files)
    {
        $this->files = [];
        array_map([$this, 'addFile'], $files);
    }

    /**
     * {@inheritdoc}
     */
    public function createTarget($file, $relPath = null)
    {
        $this->addFile(new File($file, $relPath));
    }

    /**
     * {@inheritdoc}
     */
    public function addFile(FileTargetInterface $target)
    {
        $this->files[] = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function dumpFiles($targetPath, $override = false)
    {
        foreach ($this->files as $file) {
            $this->dumpFile($file, $targetPath, $override);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException if source is not valid.
     *
     * @return string
     */
    public function dumpFile(FileTargetInterface $file, $targetPath, $override = false)
    {
        if (!$file->isValid()) {
            throw new MissingFileException(sprintf('Source file "%s" does not exist.', $file->getSource()));
        }

        $target = $this->getTargetPath($file, $targetPath);

        if ($this->backupIfOverride($this->fs, $target, $override)) {
            $this->fs->setContents($target, $file->getContents());

            return $target;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetPath(FileTargetInterface $file, $targetPath)
    {
        $path = (null !== $file->getRelativePath()) ?
            $this->expandPath(rtrim($targetPath, '\\\/').DIRECTORY_SEPARATOR.trim($file->getRelativePath(), '\\\/')) :
            $targetPath;

        return $path . DIRECTORY_SEPARATOR . $file->getFilename();
    }
}
