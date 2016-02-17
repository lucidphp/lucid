<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Request;

/**
 * @interface ContextInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ContextInterface
{

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getPath();

    /**
     * Get the request method.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Get the requests query string if any.
     *
     * @return string
     */
    public function getQueryString();

    /**
     * Get the requests host name.
     *
     * @return string
     */
    public function getHost();

    /**
     * Get the request scheme.
     *
     * @return string
     */
    public function getScheme();
}
