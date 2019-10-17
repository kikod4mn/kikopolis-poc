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

    public static function ruleSet(array $data, array $rules)
    {
        self::$rule_set = $rules;
        self::rules();
        self::params();
        return self::validate($data);
    }

    protected static function validate($data)
    {
        foreach ($data as $key => $value) {
            if (\array_key_exists($key, self::$rule_set)) {
                foreach (self::$rule_set[$key] as $rule) {
                    if (!self::handle($rule, $value)) {
                        throw new \Exception("Validation error for {$rule} with {$key} {$value}");
                    }
                }
            }
            return true;
        }
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

    protected static function handle($rule, $string)
    {
            if (\is_array($rule)) {
                $func = $rule[0];
                $param = $rule[1];
                return self::$func($string, $param);
            }
            return self::$rule($string);
    }

    public static function enforce($rule, $subject)
    {
        $result = call_user_func($rule, $subject);
    }

    private static function required($subject): bool
    {
        return Str::hasValue($subject);
    }

    private static function min($subject, $min): bool
    {
        return Str::hasLengthGreater($subject, (int) $min);
    }

    private static function max($subject, $max): bool
    {
        return Str::hasLengthLess($subject, (int) $max);
    }

    private static function string($subject)
    {
        return \is_string($subject);
    }

    private static function number($subject)
    {
        $numbers = '/[0-9]/i';

        return \is_numeric($subject) || \preg_match($numbers, $subject);
    }

    private static function symbol($subject)
    {
        $symbols = '/[\!\#\¤\%\&\(\)\=\?\@\£\$\€\{\[\]\}]/i';

        return \preg_match($symbols, $subject);
    }

    private static function letter($subject)
    {
        $letters = '/[a-z]/i';

        return \preg_match($letters, $subject);
    }

    private static function email($subject)
    {
        return Str::email($subject);
    }

    private static function unique($subject, $field)
    {
        return true;
        // Get info from the db of the unique field
    }
}
