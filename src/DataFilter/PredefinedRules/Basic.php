<?php


/*
 * This file is part of DataFilter.
 *
 * (c) Ulrich Kautz <ulrich.kautz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DataFilter\PredefinedRules;

/**
 * Basic predefined validation rules
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */

class Basic
{

    /**
     * Check for min length
     *
     * @param int  $len  Minimal length
     *
     * @return callable
     */
    public static function ruleLenMin($len)
    {
        return function ($input) use ($len) {
            return strlen($input) >= $len;
        };
    }

    /**
     * Check for max length
     *
     * @param int  $len  Maximal length
     *
     * @return callable
     */
    public static function ruleLenMax($len)
    {
        return function ($input) use ($len) {
            return strlen($input) <= $len;
        };
    }

    /**
     * Check for length range (min, max)
     *
     * @param int  $min  Minimal length
     * @param int  $max  Maximal length
     *
     * @return callable
     */
    public static function ruleLenRange($min, $max)
    {
        return function ($input) use ($min, $max) {
            return strlen($input) >= $min && strlen($input) <= $max;
        };
    }

    /**
     * Check regex against input
     *
     * @param string  $regex  Name of the rule (unique per attribute)
     *
     * @return callable
     */
    public static function ruleRegex($regex)
    {
        $args = func_get_args();
        $regex = join(':', $args);

        /*not in format "/../<modifier>", "#..#<modifier>"  nor "~..~<modifier>" */
        if (!preg_match('/^([\/#~]).+\1[msugex]*$/', $regex)) {
            $regex = '/'. stripslashes($regex). '/';
        }

        return function ($input) use ($regex) {
            return preg_match($regex, $input);
        };
    }

    /**
     * Check regex against input and returns reveresed result
     *
     * @param string  $regex  Name of the rule (unique per attribute)
     *
     * @return callable
     */
    public static function ruleRegexInverse($regex)
    {
        $args = func_get_args();
        $regex = join(':', $args);

        /*not in format "/../<modifier>", "#..#<modifier>"  nor "~..~<modifier>" */
        if (!preg_match('/^([\/#~]).+\1[msugex]*$/', $regex)) {
            $regex = '/'. stripslashes($regex). '/';
        }

        return function ($input) use ($regex) {
            return !preg_match($regex, $input);
        };
    }

    /**
     * Check whether input os numeric using "is_numeric()" method
     *
     * @return callable
     */
    public static function ruleNumber()
    {
        return function ($input) {
            return is_numeric($input);
        };
    }

    /**
     * Check whether input is numeric using "is_numeric()" method
     *
     * @return callable
     */
    public static function ruleInt()
    {
        return function ($input) {
            return function_exists('ctype_digit') ? ctype_digit(''. $input) : preg_match('/^[0-9]+$/', $input);
        };
    }

    /**
     * Check whether input is alpha-numeric using either ctype_alnum or [0-9a-zA-Z]
     *
     * @return callable
     */
    public static function ruleAlphanum()
    {
        return function ($input) {
            return function_exists('ctype_alnum') ? ctype_alnum(''. $input) : preg_match('/^[0-9a-zA-Z]+$/', $input);
        };
    }

    /**
     * Check whether input is in given array..
     *
     * @return callable
     */
    public static function ruleInArray()
    {
        $check = func_get_args();
        return function ($input) use ($check) {
            return in_array($input, $check);
        };
    }

    /**
     * Check whether input is a date string
     *
     * @return callable
     */
    public static function ruleDate()
    {
        return function ($input) {
            $date = null;
            try {
                $date = date_parse($input);
                if ($date['warning_count'] === 0 && $date['error_count'] === 0) {
                    return strlen($date['hour']) === 0;
                }
                else {
                    return false;
                }
            }
            catch(\Exception $e) {
                return false;
            }
        };
    }

    /**
     * Check whether input is a time string
     *
     * @return callable
     */
    public static function ruleTime()
    {
        return function ($input) {
            $date = null;
            try {
                $date = date_parse("2012-01-01 $input");
                if ($date['warning_count'] === 0 && $date['error_count'] === 0) {
                    return strlen($date['hour']) > 0;
                }
                else {
                    return false;
                }
            }
            catch(\Exception $e) {
                return false;
            }
        };
    }

    /**
     * Check whether input is a datetime
     *
     * @return callable
     */
    public static function ruleDateTime()
    {
        return function ($input) {
            $date = null;
            try {
                $date = date_parse($input);
                return $date['warning_count'] === 0 && $date['error_count'] === 0;
            }
            catch(\Exception $e) {
                return false;
            }
        };
    }

    /**
     * Check whether input is URL compatible
     *
     * @return callable
     */
    public static function ruleUrlPart()
    {
        return function ($input) {
            return preg_match('/^(?:[0-9a-z]+[\-_~\.])*[0-9a-z]+$/i', $input);
        };
    }

    /**
     * Check whether input is URL compatible (including unicode letters)
     *
     * @return callable
     */
    public static function ruleUrlPartUnicode()
    {
        return function ($input) {
            return preg_match('/^(?:[0-9\p{L}]+[\-_~\.])*[0-9\p{L}]+$/', $input);
        };
    }

    /**
     * Checks whether given input is a syntactical correct email
     * Using a variation of the regex given on http://www.regular-expressions.info/email.html
     *
     * @return callable
     */
    public static function ruleEmail()
    {
        return function ($input) {
            return preg_match('/^[a-z0-9._%+-]+@(?:[a-z0-9-]+\.?)*[a-z0-9]+\.[a-z]{2,4}$/i', $input);
        };
    }





}
