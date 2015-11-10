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
 * @class PermissionInterface
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface PermissionInterface
{
    const V_PUBLIC = 'public';
    const V_PRIVATE = 'private';

    /**
     * setMod
     *
     * @param mixed $mod
     *
     * @return void
     */
    public function setMode($mode);

    /**
     * getMod
     *
     * @return int
     */
    public function getMode();

    /**
     * setVisibility
     *
     * @param string $visibility
     *
     * @return void
     */
    public function setVisibility($visibility);

    /**
     * getVisibility
     *
     * @return string
     */
    public function getVisibility();

    /**
     * isPublic
     *
     * @return boolean
     */
    public function isPublic();

    /**
     * modAsString
     *
     * @return string
     */
    public function modeAsString();
}
