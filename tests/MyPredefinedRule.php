<?php


class MyPredefinedRule
{

    public static function ruleMyRule()
    {
        return function($input) {
            return $input === 'ok';
        };
    }
}