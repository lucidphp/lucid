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

use Lucid\Mux\Request\ContextInterface as RequestContext;

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
     * @return bool
     */
    public function isMatch();

    /**
     * @return bool
     */
    public function isHostMissmatch();

    /**
     * @return bool
     */
    public function isMethodMissmatch();

    /**
     * @return bool
     */
    public function isSchemeMissMatch();

    /**
     * getName
     *
     * @return string
     */
    public function getName();

    /**
     * Get the request Context.
     *
     * @return string
     */
    public function getRequest();

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
    public function getVars();
}
