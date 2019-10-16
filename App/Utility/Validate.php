<?php

declare(strict_types=1);

namespace Kikopolis\App\Utility;

use Kikopolis\App\Helpers\Str;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Data validation helper methods.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Validate
{
    protected static $rule_set = [];

    /**
     * Check data presence
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * @param   mixed  $data   The variable to test for data
     * @return  boolean
     */
    public static function hasValue($data)
    {
        $data = trim($data);

        return isset($data);
    }

    public static function ruleSet(array $data, array $rules): bool
    {
        self::$rule_set = $rules;
        self::rules();
        self::params();


        var_dump(self::$rule_set);
die;
        return true;
    }

    protected static function rules(): void
    {
        foreach (self::$rule_set as $rule => $value) {
            self::$rule_set[$rule] = explode('|', $value);
        }
    }

    protected static function params(): void
    {
        foreach (self::$rule_set as $rule => $rules) {
            foreach ($rules as $key => $params) {
                if (Str::contains($params, ':')) {
                    self::$rule_set[$rule][$key] = explode(':', $params);
                }
            }
        }
    }

    protected static function handle($string)
    {
        foreach (self::$rule_set as $rule) {
            if (\is_array($rule)) {
                call_user_func($rule[0], $string, $rule[1]);
            }
            call_user_func($rule[0], $string);
        }
    }

    public static function enforce($rule, $subject)
    {
        $result = call_user_func($rule, $subject);
    }

    private static function required($subject): bool
    {
        return Str::hasValue($subject);
    }

    private static function min($subject, int $min): bool
    {
        return Str::hasLengthGreaterThan($subject, $min);
    }

    private static function max($subject, int $max): bool
    {
        return Str::hasLengthLessThan($subject, $max);
    }

    private static function type($subject, string $required_type): bool
    {
        $string = '/string/i';
        $number = '/number/i';

        switch ($required_type) {
            case (preg_match($string, $required_type) === true):
                return is_string($subject);
            case (preg_match($number, $required_type) === true):
                return is_numeric($subject);
            default:
                return false;
        }
    }

    private static function include($subject, string $include): bool
    {
//        $symbols = '!#¤%&()=?@£$€{[]}';
        $symbol = '';
        $letter = '';
        $number = '';
        $symbols = '/[\!\#\¤\%\&\(\)\=\?\@\£\$\€\{\[\]\}]/i';
        $letters = '/[a-z]/i';
        $numbers = '/[0-9]/i';

        switch ($include) {
            case (preg_match($symbol, $include)):
                return preg_match($symbols, $subject);
            case (preg_match($letter, $include)):
                return preg_match($letters, $subject);
            case (preg_match($number, $include)):
                return preg_match($numbers, $subject);
            default:
                return false;
        }
    }

    private function unique($subject, $field)
    {
        // Get info from the db of the unique field
    }
}
