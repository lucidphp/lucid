<?php

/*
 * This File is part of the Lucid\Http\Cookie package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Cookie;

/**
 * @interface CookieJarInterface
 *
 * @package Lucid\Http\Cookie
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CookieJarInterface
{
    const OUTPUT_FLAT = true;
    const OUTPUT_NESTED = false;
    const DOMAIN_DEFAULT = '__default';

    /**
     * Add a cookie to the jar.
     *
     * @param CookieInterface $cookie
     *
     * @return void
     */
    public function set(CookieInterface $cookie);

    /**
     * Checks if a cookie exists in the jar.
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     *
     * @return boolean
     */
    public function has($name, $path = '/', $domain = null);

    /**
     * Get a cookie by name, path, and domain.
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     *
     * @return void
     */
    public function get($name, $path = '/', $domain = null);

    /**
     * Remove a cookie from the jar.
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     *
     * @return boolean `FALSE` if the cookie was not removed.
     */
    public function remove($name, $path = '/', $domain = null);

    /**
     * Remove cookies by domain from the jar.
     *
     * @param string $domain
     *
     * @return void
     */
    public function removeByDomain($domain = self::DOMAIN_DEFAULT);

    /**
     * Sets a cookie as cleared.
     *
     * This will clear the cookie once it is send to the client.
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param boolean $secrure
     * @param boolean $httpOnly
     *
     * @return void
     */
    public function clear($name, $path = '/', $domain = null, $secrure = false, $httpOnly = true);

    /**
     * Sets a cookie as cleared.
     *
     * This will clear the cookie once it is send to the client.
     *
     * @param CookieInterface $cookie
     *
     * @return void
     */
    public function setCleared(CookieInterface $cookie);

    /**
     * Get all cookies stored in the jar.
     *
     * @param boolean $flat
     *
     * @return void
     */
    public function all($flat = self::OUTPUT_FLAT);
}
