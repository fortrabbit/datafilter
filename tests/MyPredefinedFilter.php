<?php

namespace DataFilter;

class MyPredefinedFilter
{

    public static function filterMyFilter()
    {
        return function($input) {
            return '['. $input. ']';
        };
    }

    public static function myFilter($input)
    {
        return '['. $input. ']';
    }
}