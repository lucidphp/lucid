<?php

/*
 * This File is part of the Lucid\Hash package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Hash;

/**
 * @class HashBcrypt
 * @see HashInterface
 *
 * @package Lucid\Hash
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class HashBcrypt implements HashInterface
{
    /**
     * defult
     *
     * @var string
     */
    private static $default = ['cost' => 7];

    /**
     * hash
     *
     * @param mixed $password
     * @param array $options
     */
    public function hash($password, array $options = null)
    {
        $options = is_array($options) ? array_merge(static::$default, $options) : static::$default;

        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * check
     *
     * @param mixed $password
     * @param string $hash
     *
     * @boolean
     */
    public function check($password, $hash, $options = null)
    {
        return password_verify($password, $hash);
    }
}
