<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Parser;

use ArrayAccess;

/**
 * @interface TokenInterface
 *
 * @package Lucid\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface TokenInterface extends ArrayAccess
{
    /** @var int */
    const T_TEXT     = 10;

    /** @var int */
    const T_VARIABLE = 22;

    /**
     * Token is variable.
     *
     * @return bool
     */
    public function isVariable();

    /**
     * Token is text.
     *
     * @return bool
     */
    public function isText();

    /**
     * Get the token value.
     *
     * @return string the value as string.
     */
    public function getValue();

    /**
     * Get the variable regular expression.
     *
     * @return string the variable regexp, `null` if token is text.
     */
    public function getRegexp();

    /**
     * Get the path segment separator.
     *
     * @return string
     */
    public function getSeparator();

    /**
     * Get the required state if token is a variable.
     *
     * @return bool `true` if required, if token is not required or not
     * a variable `false`.
     */
    public function isRequired();
}
