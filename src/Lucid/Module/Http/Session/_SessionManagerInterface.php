<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session;

/**
 * @interface SessionManagerInterface
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SessionManagerInterface
{
    /**
     * Check if this session id exists.
     *
     * @param string $sessId
     *
     * @return boolean
     */
    public function has($sessId);

    /**
     * Gets a session by id
     *
     * @param mixed $id
     *
     * @return SessionInterface|null
     */
    public function get($sessId);

    /**
     * Cecks if this session object is available
     *
     * @param SessionInterface $session
     *
     * @return boolean
     */
    public function hasSession(SessionInterface $session);

    /**
     * Starts a session.
     *
     * @param SessionInterface $session
     *
     * @return boolean
     */
    public function startSession(SessionInterface $session);

    /**
     * Stops a session
     *
     * @param SessionInterface $session
     *
     * @return boolean
     */
    public function stopSession(SessionInterface $session);

    /**
     * Creates a new session cookie.
     *
     * @param SessionInterface $session
     *
     * @return void
     */
    public function createCookie(SessionInterface $session);

    /**
     * Creates a new Session
     *
     * @param string $id
     *
     * @return SessionInterface
     */
    public function createSession($id = null);

    /**
     * Check weather this manager can persist the current session.
     *
     * @return boolean
     */
    public function isPersistent();

    /**
     * Get the session cookie key.
     *
     * @return Lucid\Module\Http\Cookie\CookieInterface
     */
    public function getSessionCookieKey();
}
