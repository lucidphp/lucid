<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource;

use Serializable;

/**
 * @interface ResourceInterface
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResourceInterface extends Serializable
{
    /**
     * Gets the resource path.
     *
     * @return string
     */
    public function getResource();

    /**
     * Check if the resource is local.
     *
     * @return bool
     */
    public function isLocal();

    /**
     * Checks if the resource is still valid.
     *
     * @param int $time timestamp to test against.
     *
     * @return bool
     */
    public function isValid($time);

    /**
     * Returns the resource path as string.
     *
     * @return string
     */
    public function __toString();
}
