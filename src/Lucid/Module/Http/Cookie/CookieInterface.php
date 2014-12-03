<?php

/*
 * This File is part of the Lucid\Module\Http\Cookie package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Cookie;

/**
 * @interface CookieInterface
 *
 * @package Lucid\Module\Http\Cookie
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CookieInterface
{
    public function getName();
    public function getValue();
    public function getDomain();
    public function getExpireTime();
    public function getPath();
    public function isSecure();
    public function isHttpOnly();
    public function isExpired();
    public function isDeleted();
    public function setDeleted();
    public function setExpired();
    public function __toString();
}
