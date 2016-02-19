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
 * @class File
 * @see FileTargetInterface
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class File implements FileTargetInterface
{
    /** @var string */
    private $file;

    /** @var string */
    private $relativePath;

    /**
     * Constructor
     *
     * @param string $file
     * @param string $relativePath
     */
    public function __construct($file, $relativePath = null)
    {
        $this->file = $file;
        $this->relativePath = $relativePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return basename($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        return file_get_contents($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return file_exists($this->file);
    }
}
