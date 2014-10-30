<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

/**
 * @interface TokenInterface
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface TokenInterface extends \ArrayAccess
{
    const T_TEXT     = 10;
    const T_VARIABLE = 22;

    /**
     * isVariable
     *
     * @return boolean
     */
    public function isVariable();

    /**
     * isText
     *
     * @return boolean
     */
    public function isText();

    /**
     * getValue
     *
     * @return string
     */
    public function getValue();

    /**
     * getRegexp
     *
     * @return string|null
     */
    public function getRegexp();

    /**
     * getSeparator
     *
     * @return string
     */
    public function getSeparator();

    /**
     * isRequired
     *
     * @return boolean
     */
    public function isRequired();
}
