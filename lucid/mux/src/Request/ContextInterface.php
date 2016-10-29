<?php declare(strict_types=1);

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
    public function getPath() : string;

    /**
     * Get the request method.
     *
     * @return string
     */
    public function getMethod() : string;

    /**
     * Get the requests query string if any.
     *
     * @return string
     */
    public function getQueryString() : string;

    /**
     * Get the requests host name.
     *
     * @return string
     */
    public function getHost() : string;

    /**
     * Get the request scheme.
     *
     * @return string
     */
    public function getScheme() : string;

    /**
     * @return int
     */
    public function getHttpPort() : int;
}
