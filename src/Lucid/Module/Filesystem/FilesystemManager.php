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

use Lucid\Module\Filesystem\Exception\MountException;

/**
 * @class FilesystemManager
 * @see MountableInterface
 *
 * @package Lucid\Module\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesystemManager implements MountableInterface
{
    /**
     * mounts
     *
     * @var array
     */
    private $mounts;

    /**
     * Constructor.
     *
     * @param array $mounts
     */
    public function __construct(array $mounts = [])
    {
        $this->setMounts($mounts);
    }

    /**
     * {@inheritdoc}
     *
     * @throws MountException if mount does not exist.
     */
    public function mount($alias)
    {
        if (isset($this->mounts[$alias = strtolower($alias)])) {
            return $this->mounts[$alias];
        }

        // throw mount error.
        throw new MountException(sprintf('Filesystem "%s" could not be mounted.', $alias));
    }

    /**
     * Adds a filesystem to the mount pool.
     *
     * @param string $name the filesystem alias.
     * @param FilesystemInterface $fs
     *
     * @return void
     */
    public function addMount($alias, FilesystemInterface $fs)
    {
        $this->mounts[strtolower($alias)] = $fs;
    }

    /**
     * Set filesystem mounts.
     *
     * @param array $mounts associative array holding filesystems.
     *
     * @return void
     */
    public function setMounts(array $mounts)
    {
        $this->mounts = [];

        foreach ($mounts as $alias => $mount) {
            $this->addMount($alias, $mount);
        }
    }
}
