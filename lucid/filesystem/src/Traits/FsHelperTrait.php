<?php

/*
 * This File is part of the Lucid\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Traits;

use Lucid\Filesystem\Exception\IOException;

trait FsHelperTrait
{
    /**
     * Get the mask value
     *
     * @param int $cmask
     *
     * @return int
     */
    public function mask($cmask)
    {
        return $cmask & ~umask();
    }

    /**
     * contentSize
     *
     * @param string $contents
     *
     * @return int
     */
    public function contentSize($contents)
    {
        return mb_strlen($contents, '8bit');
    }

    /**
     * Ensures a local directory is writable.
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     * @throws \RuntimeException
     * @return void
     */
    public function ensureWritable($dir)
    {
        if (is_writable($dir)) {
            return;
        }
            //try to set the directory to be writable.
        if (true !== @chmod($dir, $this->mask(0775))) {
            throw new \RuntimeException(sprintf('trying to write to directory %s but it\'s not writable', $dir));
        }
    }
}
