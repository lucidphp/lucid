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

use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Request\ContextInterface as Request;

/**
 * @interface UrlGeneratorInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface UrlGeneratorInterface
{
    /** @var int */
    const RELATIVE_PATH = 0;

    /** @var int */
    const ABSOLUTE_PATH = 1;

    /**
     * Set the current request context.
     *
     * @param RequestContextInterface $request the request context.
     *
     * @return void
     */
    public function setRequestContext(Request $request);

    /**
     * Get the current request context.
     *
     * @return RequestContextInterface the request context, `NULL` if none.
     */
    public function getRequestContext();

    /**
     * Set the routecollection needed for url generation by name.
     *
     * @param RouteCollectionInterface $routes the route collection.
     *
     * @return void
     */
    public function setRoutes(RouteCollectionInterface $request);

    /**
     * Gets the current url ommitting the query string.
     *
     * @param int $type the path format type
     *
     * @return string the current url, `NULL` if `$type` is invalid.
     */
    public function currentPath($type = self::RELATIVE_PATH);

    /**
     * Gets the current url including the query string.
     *
     * @param int $type the path format type
     *
     * @return string the current url, `NULL` if `$type` is invalid.
     */
    public function currentUrl($type = self::RELATIVE_PATH);

    /**
     * Generates a readable path or url from a given route name.
     *
     * @param string $name the route name
     * @param array  $parameters parameters required by the route object
     * @param string $host host name required by the route object.
     * @param int    $type the path format type
     *
     * @return string
     */
    public function generate($name, array $parameters = [], $host = null, $type = self::RELATIVE_PATH);
}
