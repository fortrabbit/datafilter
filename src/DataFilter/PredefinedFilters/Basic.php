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
     * Returns web/url compliant string, eg used for URL path name
     *
     * @return callable
     */
    public static function filterWebCompliant()
    {
        return function ($input) {
            return
                preg_replace( '/(?:^\-+|\-+$)/', '',    // tailing|leading "-"
                preg_replace('/\-\-+/', '-',            // more than one "-"
                preg_replace('/[^a-z0-9\-_\.~]/', '-',  // all
                    strtolower($input))
                ));
        };
    }

    /**
     * Returns web/url compliant string, including unicode (eg cyrillic, arabic and whatnot) letter
     *
     * @return callable
     */
    public static function filterWebCompliantUnicode()
    {
        return function ($input) {
            return
                preg_replace( '/(?:^\-+|\-+$)/', '',    // tailing|leading "-"
                preg_replace('/\-\-+/', '-',            // more than one "-"
                preg_replace('/[^\p{L}0-9\-_\.~]/u', '-',  // all
                    strtolower($input))
                ));
        };
    }


}
