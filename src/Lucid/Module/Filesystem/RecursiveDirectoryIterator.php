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
 * @class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
 * @see \RecursiveDirectoryIterator
 *
 * @package Selene\Module\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    /**
     * Creates a new Recursive Directory Iterator.
     *
     * @param string  $path
     * @param integer $flags
     * @throws \InvalidArgumentException if $flags contain an
     * CURRENT_AS_* flag other then CURRENT_AS_FILEINFO.
     *
     * @access public
     */
    public function __construct($path, $flags)
    {
        if ($flags & (\FilesystemIterator::CURRENT_AS_SELF|\FilesystemIterator::CURRENT_AS_PATHNAME)) {
            throw new \InvalidArgumentException(
                sprintf('%s only supports FilesystemIterator::CURRENT_AS_FILEINFO', __CLASS__)
            );
        }

        parent::__construct($path, $flags);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return new SplFileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
    }
}
