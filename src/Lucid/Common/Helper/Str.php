<?php

/*
 * This File is part of the Lucid\Common\Helper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Helper;

/**
 * @class Str
 *
 * @package Lucid\Common\Helper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Str
{
    /**
     * rchars
     *
     * @var string
     */
    private static $rchars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * lowDash
     *
     * @param string $string
     * @param string $delim
     *
     * @return string
     */
    public static function lowDash($string, $delim = '_')
    {
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
    public static function snakeCase($string, $delim = '_')
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
    public static function camelCase($string, array $replacement = ['-' => ' ', '_' => ' '])
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
    public static function camelCaseAll($string, array $replacement = ['-' => ' ', '_' => ' '])
    {
        return strtr(ucwords(strtr($string, $replacement)), [' ' => '']);
    }

    /**
     * equals
     *
     * @param string $string
     * @param string $input
     *
     * @return boolean
     */
    public static function equals($string, $input)
    {
        return 0 === strcmp($string, $input);
    }

    /**
     * safeCmp
     *
     * @param string $string
     * @param string $input
     *
     * @return boolean
     */
    public static function safeCmp($string, $input)
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
     * rand
     *
     * @param int $length
     *
     * @return string
     */
    public static function rand($length)
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Str::rand() expects first argument to be integer, instead saw %s.',
                    gettype($length)
                )
            );
        }

        if (!function_exists('openssl_random_pseudo_bytes')) {
            return self::quickRand($length);
        }

        if (null === ($bytes = openssl_random_pseudo_bytes($length * 2))) {
            throw new \RuntimeException('Cannot generate random string');
        }

        return substr(str_replace(['/', '=', '+'], '', base64_encode($bytes)), 0, $length);
    }

    /**
     * strQuickRand
     *
     * @param mixed $length
     *
     * @access public
     * @return string
     */
    public static function quickRand($length)
    {
        return substr(str_shuffle(str_repeat(static::$rchars, 5)), 0, $length);
    }

    private function __construct()
    {
    }
}
