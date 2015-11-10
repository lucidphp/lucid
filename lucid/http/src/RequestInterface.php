<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @interface RequestInterface
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RequestInterface extends ServerRequestInterface
{
    const T_MAIN = 1;
    const T_SUB  = 2;

    /**
     * Get the request URI
     *
     * @return void
     */
    //public function getRequestUri();

    /**
     * Get the request path info.
     *
     * @return string
     */
    public function getPathInfo();

    /**
     * Get the request host name.
     *
     * @return string
     */
    public function getHost();

    /**
     * Get the port of the current request.
     *
     * @return int
     */
    public function getPort();
}
