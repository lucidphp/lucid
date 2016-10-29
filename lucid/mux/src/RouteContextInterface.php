<?php declare(strict_types=1);

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
    public function getRegex(bool $raw = false) : string;

    /**
     * getStaticPath
     *
     * @return string
     */
    public function getStaticPath() : string;

    /**
     * getParameters
     *
     * @return array
     */
    public function getVars() : array;

    /**
     * getTokens
     *
     *
     * @return \Lucid\Mux\Parser\TokenInterface[]
     */
    public function getTokens() : array;

    /**
     * getHostRegexp
     *
     *
     * @var bool $raw
     *
     * @return string
     */
    public function getHostRegex(bool $raw = false) : string;

    /**
     * getHostParameters
     *
     * @return array
     */
    public function getHostVars() : array;

    /**
     * getHostTokens
     *
     * @return \Lucid\Mux\Parser\TokenInterface[]
     */
    public function getHostTokens() : array;
}
