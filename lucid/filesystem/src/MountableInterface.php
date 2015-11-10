<?php

/*
 * This File is part of the Lucid\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem;

/**
 * @interface MountableInterface
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MountableInterface
{
    /**
     * Mounts a filesystem.
     *
     * @param string $name
     *
     * @return FilesystemInterface
     */
    public function mount($name);
}
