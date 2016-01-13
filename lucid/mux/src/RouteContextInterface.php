<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

/**
 * @interface RouteExpressionInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteContextInterface
{
    /**
     * getRegexp
     *
     *
     * @return void
     */
    public function getRegexp($raw = false);

    /**
     * getStaticPath
     *
     *
     * @return void
     */
    public function getStaticPath();

    /**
     * getParameters
     *
     *
     * @return void
     */
    public function getParameters();

    /**
     * getTokens
     *
     *
     * @return void
     */
    public function getTokens();

    /**
     * getHostRegexp
     *
     *
     * @return void
     */
    public function getHostRegexp($raw = false);

    /**
     * getHostParameters
     *
     * @return void
     */
    public function getHostParameters();

    /**
     * getHostTokens
     *
     * @return void
     */
    public function getHostTokens();
}
