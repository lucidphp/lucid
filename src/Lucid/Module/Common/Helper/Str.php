<?php

/*
 * This File is part of the Lucid\Module\Common\Helper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Common\Helper;

/**
 * @class Str
 *
 * @package Lucid\Module\Common\Helper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Str
{
    /**
     * lowDash
     *
     * @param mixed $string
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
     * @param mixed $string
     * @param string $delim
     *
     * @return string
     */
    public static function snakeCase($string, $delim = '_')
    {
        return strtolower(preg_replace('#[A-Z]#', $delim.'$0', lcfirst($string)));
    }

    /**
     * equals
     *
     * @param mixed $string
     * @param mixed $input
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
        $pad = static::strRand(4);

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

    private function __construct()
    {
    }
}
