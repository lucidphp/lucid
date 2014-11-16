<?php

/*
 * This File is part of the Lucid\Module\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Module\Filesystem;

/**
 * @class SplFileInfo extends \SplFileInfo implements ArrayableInterface
 * @see ArrayableInterface
 * @see \SplFileInfo
 *
 * @package Selene\Module\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class SplFileInfo extends \SplFileInfo
{
    /**
     * relativePath
     *
     * @var string
     */
    private $relativePath;

    /**
     * relativePathName
     *
     * @var string
     */
    private $relativePathName;

    /**
     * @param mixed $file
     * @param mixed $relativePath
     * @param mixed $relativePathName
     *
     * @access public
     * @return mixed
     */
    public function __construct($file, $relativePath = null, $relativePathName = null)
    {
        parent::__construct($file);

        $this->relativePath = $relativePath;

        $this->relativePathName = $relativePathName;
    }

    /**
     * getRelativePath
     *
     * @access public
     * @return string
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * getRelativePathName
     *
     * @access public
     * @return string
     */
    public function getRelativePathName()
    {
        return $this->relativePathName;
    }

    /**
     * toArray
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        $attributes = [
            'name'             => $this->getBasename(),
            'path'             => $this->getRealPath(),
            'relativePath'     => $this->getRelativePath(),
            'relativePathName' => $this->getRelativePathName(),
            'lastmod'          => $this->getMTime(),
            'type'             => $this->getType(),
            'owner'            => $this->getOwner(),
            'group'            => $this->getGroup(),
            'size'             => $this->getSize()
        ];

        if ($this->isFile()) {
            $attributes['extension'] = $this->getExtension();
            $attributes['mimetype']  = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->getRealPath());
        }

        return $attributes;
    }
}
