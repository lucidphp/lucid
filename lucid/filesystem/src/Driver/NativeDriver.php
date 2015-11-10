<?php

/*
 * This File is part of the Lucid\Filesystem\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Driver;

use SplFileInfo;

/**
 * @class NativeDriver
 *
 * @package Lucid\Filesystem\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class NativeDriver extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        return file_exists($this->getPrefixed($path));
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        return is_dir($this->getPrefixed($path));
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        return is_file($this->getPrefixed($path));
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($path)
    {
        return is_link($this->getPrefixed($path));
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo($path)
    {
        $info = new SplFileInfo($this->getPrefixed($path));

        return $this->createPathInfo($this->fileInfoToPathInfo($path, $info));
    }

    /**
     * fileInfoToPathInfo
     *
     * @param SplFileInfo $spl
     *
     * @return PathInfo
     */
    protected function fileInfoToPathInfo($path, SplFileInfo $spl)
    {
        $info = compact('path');

        if (!$spl->isDir()) {
            $info['size'] = $info->getSize();
        }

        $info['timestamp'] = $info->getMTime();
    }
}
