<?php


/*
 * This file is part of DataFilter.
 *
 * (c) Ulrich Kautz <ulrich.kautz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DataFilter;

/**
 * Utilities for data filter
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */

class Util
{
    public static $FLATTEN_SEPARATOR = '.';

    /**
     * Formats string by replacing ":variable:" with given values
     *
     * @param string  $str   Input string
     * @param array   $args  Variables to be replaced
     *
     * @return string
     */
    public static function formatString($str, $args)
    {
        foreach ($args as $k => $v) {
            $str = preg_replace('/:'. $k. ':/', $v, $str);
        }
        return $str;
    }


    /**
     * Flattens input data
     *
     * @param string  $str   Input string
     * @param array   $args  Variables to be replaced
     *
     * @return array
     */
    public static function flatten($data, $flat = array(), $prefix = '')
    {
        foreach ($data as $key => $value) {

            // is array -> flatten deeped
            if (is_array($value)) {
                $flat = self::flatten($value, $flat, $prefix. $key. self::$FLATTEN_SEPARATOR);
            }
            // scalar -> use
            else {
                $flat[$prefix. $key] = $value;
            }
        }
        return $flat;
    }
}
