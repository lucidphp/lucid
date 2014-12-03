<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Request;

use SplFileInfo;
use LogicException;

/**
 * @class UploadedFile
 * @see UploadedFileInterface
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class UploadedFile implements UploadedFileInterface
{
    protected $size;
    protected $mime;
    protected $name;
    protected $error;

    public function __construct(array $files)
    {
        $this->setAttributesFromArray($files);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        if (!$this->isError()) {
            return $this->path;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        if (!$this->isError()) {
            return $this->name;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        if ($this->isError()) {
            return '';
        }

        if (null === $this->mime) {
            $this->mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
        }

        return $this->mime;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        if ($this->isError()) {
            return 0;
        }

        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function move($path, $nameOverride = null)
    {
        if ($this->isError()) {
            throw new LogicException;
        }

        $target = $path.DIRECTORY_SEPARATOR.($nameOverride ?: $this->name);

        if (move_uploaded_file($this->path, $target)) {
            return new SplFileInfo($target);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isError()
    {
        return $this->error > UPLOAD_ERR_OK;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode()
    {
        return $this->error;
    }

    protected function setAttributesFromArray(array $file)
    {
        $this->name  = $file['name'];
        $this->path  = $file['tmp_name'];
        $this->size  = (int)$file['size'];
        $this->error = (int)$file['error'];
    }
}
