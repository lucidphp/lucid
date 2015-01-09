<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Cache;

/**
 * @class CacheInterface
 *
 * @package Lucid\Module\Filesystem\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CacheInterface extends CacheableInterface
{
    /**
     * hasPathInfo
     *
     * @return boolean
     */
    public function hasPathInfo($path);

    /**
     * getPathInfo
     *
     * @return array
     */
    public function getPathInfo($path);

    /**
     * updateFileObject
     *
     * @param arra $data
     *
     * @return void
     */
    public function updateFileObject($path, array $data);

    /**
     * removeFileObject
     *
     * @param mixed $path
     *
     * @return boolean
     */
    public function removeFileObject($path);

    /**
     * persist
     *
     * @return boolean
     */
    public function persist();
}
