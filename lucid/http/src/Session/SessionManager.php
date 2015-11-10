<?php

/*
 * This File is part of the Lucid\Http\Session package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session;

/**
 * @class SessionManager
 *
 * @package Lucid\Http\Session
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SessionManager implements SessionManagerInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * {@inheritdoc}
     */
    public function start(RequestInterface $request)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function stop(CookieJarInterface $cookies)
    {
    }
}
