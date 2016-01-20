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
	 * @var bool $raw
     *
     * @return string
     */
	public function getRegex($raw = false);

    /**
     * getStaticPath
     *
	 * @return string
     */
    public function getStaticPath();

    /**
     * getParameters
     *
     * @return array
     */
    public function getParameters();

    /**
     * getTokens
     *
     *
	 * @return array `Lucid\Mux\Parser\TokenInterface[]`
     */
    public function getTokens();

    /**
     * getHostRegexp
     *
     *
	 * @var bool $raw
     *
	 * @return string
     */
	public function getHostRegex($raw = false);

    /**
     * getHostParameters
	 *
     * @return array
     */
    public function getHostParameters();

    /**
     * getHostTokens
     *
	 * @return array `Lucid\Mux\Parser\TokenInterface[]`
     */
    public function getHostTokens();
}
