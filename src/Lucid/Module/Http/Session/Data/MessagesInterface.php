<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Data;

/**
 * @interface MessagesInterface
 *
 * @package Lucid\Module\Http\Session\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MessagesInterface extends AttributesInterface
{
    /**
     * Gets a message by key an deletes it from the pool
     *
     * @param string $message
     * @param mixed $default
     *
     * @return void
     */
    public function flush($message, $default = null);

    /**
     * Gets all messages an deletes them from the pool.
     *
     * @return array
     */
    public function flushALl();
}
