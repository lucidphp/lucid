<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Matcher;

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
     * isMatch
     *
     * @return boolean
     */
    public function isMatch();

    /**
     * getName
     *
     * @return string
     */
    public function getName();

    /**
     * getPath
     *
     * @return string
     */
    public function getPath();

    /**
     * getHandler
     *
     * @return mixed|string
     */
    public function getHandler();

    /**
     * getParameters
     *
     * @return array
     */
    public function getParameters();
}
