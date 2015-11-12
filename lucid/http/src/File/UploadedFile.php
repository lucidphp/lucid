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

use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @class UploadedFile
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class UploadedFile implements UploadedFileInterface
{
    /** @var FileInfo */
    private $info;

    /** @var FileStream */
    private $stream;

    /**
     * Constructor.
     *
     * @param FileInfo $info
     */
    public function __construct(FileInfo $info)
    {
        $this->info = $info;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientFilename()
    {
        return $this->info->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->info->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        if ($this->isError()) {
            return 0;
        }

        return $this->info->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        if (!file_exists($this->info->tempName)) {
            throw new RuntimeException();
        }

        if (null === $this->stream) {
            $this->stream = new FileStream($this->info);
        }

        return $this->stream;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType()
    {
        if (null === $this->info->type) {
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->info->tmpName);
        }

        return $this->info->type;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($targetPath)
    {
        if ($this->isError()) {
            throw new RuntimeException(sprintf('Error %d.', $this->info->error));
        }

        if (is_file($targetPath)) {
            throw new InvalidArgumentException(sprintf('Target file %s exists', $targetPath));
        }

        if (!is_dir($dir = dirname($targetPath))) {
            throw new InvalidArgumentException(sprintf('Target path %s does not exists.', $dir));
        }

        if (false === rename($this->info->name, $targetPath)) {
            throw new RuntimeException(sprintf('Error moving file %s', $this->info->name));
        }
    }

    /**
     * isError
     *
     * @return bool
     */
    private function isError()
    {
        if (null === $this->info->error) {
            return false;
        }

        return $this->info->error > UPLOAD_ERR_OK;
    }
}
