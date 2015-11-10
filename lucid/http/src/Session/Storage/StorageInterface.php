<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session\Storage;

/**
 * @interface SessionStorageInterface
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface StorageInterface
{
    /**
     * start
     *
     * @return boolean
     */
    public function start();

    /**
     * Save an stop the session.
     *
     * @return boolean
     */
    public function save();

    /**
     * getId
     *
     * @return string
     */
    public function getId();

    /**
     * getName
     *
     * @return string
     */
    public function getName();

    /**
     * setId
     *
     * @param string $id
     *
     * @return void
     */
    public function setId($id);

    /**
     * setName
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name);

    /**
     * regenerate
     *
     * @param boolean $destroy
     * @param int $ttl
     *
     * @return boolean
     */
    public function regenerate($destroy = false, $ttl = null);

    /**
     * clear
     *
     * @return void
     */
    public function clear();

    /**
     * isActive
     *
     * @return boolean
     */
    public function isActive();

    /**
     * isStarted
     *
     * @return boolean
     */
    public function isStarted();

    /**
     * isClosed
     *
     * @return boolean
     */
    public function isClosed();
}
