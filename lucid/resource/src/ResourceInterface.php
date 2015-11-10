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
     * getResource
     *
     * @return string
     */
    public function getResource();

    /**
     * isLocal
     *
     * @return bool
     */
    public function isLocal();

    /**
     * isValid
     *
     * @param mixed $time
     *
     * @return bool
     */
    public function isValid($time);
}
