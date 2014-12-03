<?php

/*
 * This File is part of the Lucid\Module\Http\Response package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Response;

use Lucid\Module\Http\ParameterMutableInterface;

/**
 * @interface ResponseHeaderInterface
 *
 * @package Lucid\Module\Http\Response
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResponseHeaderInterface extends ParameterMutableInterface
{
    /**
     * Set an ETag header
     *
     * @param string $etag the etag content.
     * @param boolean $weak
     *
     * @return void
     */
    public function setEtag($etag = null, $weak = false);

    /**
     * hasCacheControl
     *
     * @return boolean
     */
    public function hasCacheControl();

    /**
     * setCacheControl
     *
     * @param string $name
     *
     * @return void
     */
    public function setCacheControl($name);

    /**
     * Send a header to the client
     *
     * @param string $header
     * @param boolean $replace
     * @param int $status
     *
     * @return void
     */
    public function send($header, $replace = false, $status = null);

    /**
     * Send all headers to the client.
     *
     * @param boolean $replace
     * @param int $status
     *
     * @return void
     */
    public function sendAll($replace = false, $status = null);

    /**
     * Add a cookie.
     *
     * @param CookieInterface $cookie
     *
     * @return void
     */
    public function addCookie(CookieInterface $cookie);

    /**
     * Get a cookie by name
     *
     * @param string $name
     *
     * @return void
     */
    public function getCookie($name);

    /**
     * Get all cookies.
     *
     * @return array
     */
    public function getCookies();

    /**
     * Removes a cookie from the pool
     *
     * @param string $name
     *
     * @return void
     */
    public function removeCookie($name, $path = '/', $domain = null);

    /**
     * clearCookie
     *
     * @param mixed $name
     * @param string $path
     * @param mixed $domain
     * @param mixed $secure
     * @param mixed $httpOnly
     *
     * @return void
     */
    public function clearCookie($name, $path = '/', $domain = null, $secure = false, $httpOnly = true);
}
