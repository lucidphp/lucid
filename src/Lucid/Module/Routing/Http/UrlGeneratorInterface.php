<?php

/*
 * This File is part of the Lucid\Module\Routing\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Http;

use Lucid\Module\Routing\RouteCollectionInterface;

/**
 * @interface UrlGeneratorInterface
 *
 * @package Lucid\Module\Routing\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface UrlGeneratorInterface
{
    /**
     * @var int
     */
    const ABSOLUTE_PATH = 22;

    /**
     * @var int
     */
    const RELATIVE_PATH = 144;

    /**
     * setRequestContext
     *
     * @param RequestContextInterface $request
     *
     * @return void
     */
    public function setRequestContext(RequestContextInterface $request);

    /**
     * setRoutes
     *
     * @param RouteCollectionInterface $request
     *
     * @return void
     */
    public function setRoutes(RouteCollectionInterface $request);

    /**
     * currentPath
     *
     * @param int $type
     *
     * @return string|null
     */
    public function currentPath($type = self::RELATIVE_PATH);

    /**
     * currentUrl
     *
     * @param int $type
     *
     * @return string|null
     */
    public function currentUrl($type = self::RELATIVE_PATH);

    /**
     * getPath
     *
     * @param mixed $name
     * @param array $parameters
     * @param mixed $host
     * @param mixed $type
     *
     * @return string
     */
    public function generate($name, array $parameters = [], $host = null, $type = self::RELATIVE_PATH);
}
