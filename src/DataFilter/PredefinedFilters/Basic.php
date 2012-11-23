<?php


/*
 * This file is part of DataFilter.
 *
 * (c) Ulrich Kautz <ulrich.kautz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DataFilter\PredefinedFilters;

/**
 * Basic predefined filters
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */

class Basic
{
    /**
     * Returns web/url compliant string
     *
     * @return callable
     */
    public static function filterTrim()
    {
        return function ($input) {
            return trim($input);
        };
    }

    /**
     * Strips input of HTML tags using the strip_tags() method
     *
     * @param string  $allowedTags  Optional string containing all allowed tags.. see strip_tags()
     *
     * @return callable
     */
    public static function filterStripHtml($allowedTags = null)
    {
        return function ($input) use($allowedTags) {
            return strip_tags($input, $allowedTags);
        };
    }

    /**
     * Transforms input into a URL-usable string.. "Bla {blub}" -> "bla-blub"
     *
     * @return callable
     */
    public static function filterUrlPart()
    {
        return function ($input) {
            return
                preg_replace( '/(?:^\-+|\-+$)/', '',     // tailing|leading "-"
                preg_replace('/\-\-+/', '-',             // more than one "-"
                preg_replace('/[^a-z0-9\-_\.~]/u', '-',  // strip not allowed chars
                    strtolower($input))
                ));
        };
    }

    /**
     * Transforms input into a URL-usable string.. "Bla {blub}" -> "bla-blub" (unicode characters allowed)
     *
     * @return callable
     */
    public static function filterUrlPartUnicode()
    {
        return function ($input) {
            return
                preg_replace( '/(?:^\-+|\-+$)/', '',       // tailing|leading "-"
                preg_replace('/\-\-+/', '-',               // more than one "-"
                preg_replace('/[^\p{L}0-9\-_\.~]/u', '-',  // strip not allowed chars
                    strtolower($input))
                ));
        };
    }

    /**
     * Transforms input into a lowercase string
     *
     * @return callable
     */
    public static function filterLowercase()
    {
        return function ($input) {
            return strtolower($input);
        };
    }

    /**
     * Transforms input into a uppercase string
     *
     * @return callable
     */
    public static function filterUppercase()
    {
        return function ($input) {
            return strtoupper($input);
        };
    }


}
