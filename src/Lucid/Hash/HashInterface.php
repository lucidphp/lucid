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
 * @interface HashInterface
 *
 * @package Lucid\Hash
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface HashInterface
{
    /**
     * Creates a hash from string
     *
     * @param string $string  string to be hased
     * @param array  $options optional configuration
     *
     * @return string the hashed input string.
     */
    public function hash($string, array $options = null);

    /**
     * Check an input value against a hash.
     *
     * @param string $string value to be compared agains hash
     * @param string $hash   hash to be compared against string
     *
     * @boolean
     */
    public function check($string, $hash, $options = null);
}
