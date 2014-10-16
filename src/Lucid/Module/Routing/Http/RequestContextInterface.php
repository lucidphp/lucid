<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Http;

/**
 * @interface RequestContextInterface
 *
 * @package Lucid\Module\Routing\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RequestContextInterface
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
