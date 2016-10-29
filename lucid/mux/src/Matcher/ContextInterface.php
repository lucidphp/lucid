<?php declare(strict_types=1);

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
     * Tells you if this match result is a match.
     *
     * @return bool
     */
    public function isMatch() : bool;

    /**
     * Tells, if a failed match is because of mismatched host.
     *
     * @return bool
     */
    public function isHostMismatch() : bool;

    /**
     * Tells, if a failed match is because of mismatched
     * http method.
     *
     * @return bool
     */
    public function isMethodMismatch() : bool;

    /**
     *
     * Tells, if a failed match is because of mismatched
     * protocol.
     *
     * @return bool
     */
    public function isSchemeMisMatch() : bool;

    /**
     * Get the matched route name.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Get the request Context.
     *
     * @return RequestContext
     */
    public function getRequest() : RequestContext;

    /**
     * Get the matched path.
     *
     * @return string
     */
    public function getPath() : string;

    /**
     * Get the handler of the match
     *
     * @return mixed|string
     */
    public function getHandler();

    /**
     * Get passed parameters if any.
     *
     * @return array
     */
    public function getVars() : array;
}
