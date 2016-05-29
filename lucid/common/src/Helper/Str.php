<?php

/*
 * This File is part of the Lucid\Common package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Helper;

use RuntimeException;
use InvalidArgumentException;

/**
 * @class Str
 *
 * @package Lucid\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Str
{
    /** @var string */
    private static $rchars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * lowDash
     *
     * @param string $string
     * @param string $delim
     * @deprecated
     *
     * @return string
     */
    public static function lowDash(string $string, string $delim = '_') : string
    {
        trigger_error(
            sprintf(
                '%1$s::%2$s will be removed in the future. Use %1$s::snakeCase instead.',
                __CLASS__,
                __METHOD__
            ),
            E_USER_DEPRECATED
        );
        return self::snakeCase($string, $delim);
    }

    /**
     * snakeCase
     *
     * @param string $string the string to convert to snake case.
     * @param string $delim world delimiter, defaults to '_'
     *
     * @return string
     */
    public static function snakeCase(string $string, string $delim = '_') : string
    {
        return strtolower(preg_replace('#[A-Z]#', $delim.'$0', lcfirst($string)));
    }

    /**
     * camelcase notataion
     *
     * @param string $str
     * @param array[string => string] $replacement
     *
     * @return string
     */
    public static function camelCase(string $string, array $replacement = ['-', '_']) : string
    {
        return lcfirst(self::camelCaseAll($string, $replacement));
    }

    /**
     * all camelcase notataion
     *
     * @param string $string
     * @param array $replacement
     *
     * @return string
     */
    public static function camelCaseAll(string $string, array $replacement = ['-', '_']) : string
    {
        $rpl = array_combine(array_values($replacement), array_pad([], count($replacement), ' '));

        return strtr(ucwords(strtr($string, $rpl)), [' ' => '']);
    }

    /**
     * Compares two strings using strcmp.
     *
     * @param string $string
     * @param string $input
     *
     * @return bool
     */
    public static function equals(string $string, string $input) : bool
    {
        return 0 === strcmp($string, $input);
    }

    /**
     * Safe compare two strings.
     *
     * @param string $string
     * @param string $input
     *
     * @return bool
     */
    public static function safeCmp(string $string, string $input) : bool
    {
        $pad = static::rand(4);

        $string .= $pad;
        $input  .= $pad;

        $strLen = mb_strlen($string);
        $inpLen = mb_strlen($input);

        $result = $strLen ^ $inpLen;

        for ($i = 0; $i < $inpLen; $i++) {
            $result |= (ord($string[$i % $strLen]) ^ ord($input[$i]));
        }

        return 0 === $result;
    }

    /**
     * Generates random strings of a given length.
     *
     * @param int $length
     *
     * @return string
     */
    public static function rand(int $length) : string
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            return self::quickRand($length);
        }

        if (null === ($bytes = openssl_random_pseudo_bytes($length * 2))) {
            throw new RuntimeException('Cannot generate random string');
        }

        return substr(str_replace(['/', '=', '+'], '', base64_encode($bytes)), 0, $length);
    }

    /**
     * Quick generation of pseudo-random strings.
     *
     * @param int $length
     *
     * @return string
     */
    public static function quickRand(int $length) : string
    {
        return substr(str_shuffle(str_repeat(static::$rchars, 5)), 0, $length);
    }

    /**
     * Disable Constructor.
     */
    private function __construct()
    {
    }
}
