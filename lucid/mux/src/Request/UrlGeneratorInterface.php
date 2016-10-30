<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
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
 * @author Thomas Appel <mail@thomas-appel.com>
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
     * @param Request $request the request context.
     */
    public function setRequestContext(Request $request) : void;

    /**
     * Get the current request context.
     *
     * @return Request the request context, `null` if none.
     */
    public function getRequestContext() : ?Request;

    /**
     * Set the routecollection needed for url generation by name.
     *
     * @param RouteCollectionInterface $routes the route collection.
     */
    public function setRoutes(RouteCollectionInterface $routes) : void;

    /**
     * Gets the current url ommitting the query string.
     *
     * @param int $type the path format type
     *
     * @return string the current url, `null` if `$type` is invalid.
     */
    public function currentPath(int $type = self::RELATIVE_PATH) : ?string;

    /**
     * Gets the current url including the query string.
     *
     * @param int $type the path format type
     *
     * @return string the current url, `null` if `$type` is invalid.
     */
    public function currentUrl(int $type = self::RELATIVE_PATH) : ?string;

    /**
     * Generates a readable path or url from a given route name.
     *
     * @param string         $name the route name
     * @param mixed[][mixed] $parameters parameters required by the route object
     * @param string         $host host name required by the route object.
     * @param int            $type the path format type
     *
     * @return string
     */
    public function generate(
        string $name,
        array $parameters = [],
        string $host = null,
        int $type = self::RELATIVE_PATH
    ) : string;
}
