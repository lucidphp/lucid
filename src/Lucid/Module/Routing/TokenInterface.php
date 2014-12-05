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
    /**
     * Token type text.
     *
     * @var int
     */
    const T_TEXT     = 10;

    /**
     * Token type variable.
     *
     * @var int
     */
    const T_VARIABLE = 22;

    /**
     * Token is variable.
     *
     * @return boolean `TRUE` or `FALSE`
     */
    public function isVariable();

    /**
     * Token is text.
     *
     * @return boolean `TRUE` or `FALSE`
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
     * @return string the variable regexp, `NULL` if token is text.
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
     * @return boolean `TRUE` if required, if token is not required or not
     * a variable `FALSE`.
     */
    public function isRequired();
}
