<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http;

/**
 * @class RequestStackInterface
 *
 * @package Lucid\Adapter\HttpFoundation
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RequestStackInterface
{
    /**
     * Push a request object on top of the stack.
     *
     * @param Request $request
     *
     * @return void
     */
    public function push(RequestInterface $request);

    /**
     * Remove the topmost request object from the stack.
     *
     * @return Request
     */
    public function pop();

    /**
     * Get the main request object.
     *
     * The main request object is considered the fist obecjted added to the
     * stack.
     *
     * @return Request
     */
    public function getMain();

    /**
     * Get the current Request object.
     *
     * @return Request
     */
    public function getCurrent();

    /**
     * Return the previous request from the stack.
     *
     * If not previous request is available,
     * the current request is returned.
     *
     * @return Request|null
     */
    public function getPrevious();

    /**
     * Check if the stack is empry.
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * Remove all request objects on the stack.
     *
     * @return void
     */
    public function removeAll();

    /**
     * Removes all request objects except the first one.
     *
     * @return void
     */
    public function removeSubRequests();
}
