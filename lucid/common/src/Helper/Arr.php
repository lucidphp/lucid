<?php

/*
 * This File is part of the Lucid\Common package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and limense information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Helper;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * @class Arr
 * @final
 *
 * @package Lucid\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Arr
{
    /** @var string */
    const NS_SEPARATOR = '.';

    /**
     * Flattens a multidimensional array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function flatten(array $array)
    {
        $out = [];

        foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $key => $item) {
            $out[$key] = $item;
        }

        return $out;
    }

    /**
     * column
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $index
     *
     * @return array
     */
    public static function column(array $array, $key, $index = null)
    {
        return array_column($array, $key, $index);
    }

    /**
     * Plucks values by key from a list of arrays or objects.
     *
     * @param array $array
     * @param string $key
     *
     * @return array
     */
    public static function pluck(array $array, $key)
    {
        return array_map(function ($item) use ($key) {
            return is_object($item) ? $item->$key : $item[$key];
        }, $array);
    }

    /**
     * Zips to or more arrays
     *
     * @return void
     */
    public static function zip(...$args)
    {
        $args = array_values($args);
        $count = count($args);

        $out = [];

        for ($i = 0; $i < $count; $i++) {
            $out[] = self::pluck($args, $i);
        }

        return $out;
    }

    /**
     * Get the highest value.
     *
     * @param array $args
     *
     * @return int
     */
    public static function max(array $args)
    {
        return count(call_user_func_array('max', $args));
    }

    /**
     * Get the lowest value.
     *
     * @param array $args
     *
     * @return int
     */
    public static function min(array $args)
    {
        return count(call_user_func_array('min', $args));
    }

    /**
     * Determines if a given array is an indexed list.
     *
     * @param array $array
     * @param bool $strict
     *
     * @return boolean
     */
    public static function isList(array $array, $strict = false)
    {
        $isNumbers = ctype_digit(implode('', $keys = array_keys($array)));

        if (!$strict) {
            return $isNumbers;
        }

        if (!$isNumbers) {
            return false;
        }

        return $keys === range(0, count($array) - 1);
    }

    /**
     * Getter for multidimensional arrays.
     *
     * @param array $array
     * @param string|null $namespace
     * @param string $separator
     *
     * @return mixed
     */
    public static function get(array $array, $namespace = null, $separator = self::NS_SEPARATOR)
    {
        if (!is_string($namespace)) {
            return $array;
        }

        $keys = explode($separator, $namespace);

        while (count($keys) > 0 && !is_null($array)) {
            $key = array_shift($keys);
            $array = isset($array[$key]) ? $array[$key] : null;
        }

        return $array;
    }

    /**
     * Sets a segmented string to an array
     *
     * @param string $namespace
     * @param mixed  $value
     * @param array  $array
     * @param string $separator
     *
     * @return array
     */
    public static function set(array &$input, $namespace, $value, $separator = self::NS_SEPARATOR)
    {
        $keys  = explode($separator, $namespace);
        $pointer = &$input;

        while (count($keys) > 0) {
            $key = array_shift($keys);
            $pointer[$key] = isset($pointer[$key]) ? $pointer[$key] : [];
            $pointer = &$pointer[$key];
        }

        $pointer = $value;

        return $input;
    }

    /**
     * Unsets a value from a multidimensional array
     *
     * @param array $array
     * @param string $namespace
     * @param string $separator
     *
     * @return void
     */
    public static function unsetKey(array &$array, $namespace, $separator = self::NS_SEPARATOR)
    {
        if (!is_string($namespace)) {
            return $array;
        }

        $keys = explode($separator, $namespace);

        while (($count = count($keys)) > 0 && !is_null($array)) {
            $key = array_shift($keys);
            if (isset($array[$key])) {
                if ($count < 2) {
                    unset($array[$key]);
                } else {
                    $array =& $array[$key];
                }
            }
        }
    }

    /**
     * Filters out boolish items that resemble none "truthy" values.
     *
     * @return array
     */
    public static function compact($array, $reindex = false)
    {
        $out = array_filter($array, function ($item) {
            return false !== (bool)$item;
        });

        return false !== $reindex && self::isList($out) ? array_values($out) : $out;
    }

    private function __construct()
    {
    }
}
