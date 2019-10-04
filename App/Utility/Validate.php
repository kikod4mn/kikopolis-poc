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
        static::$rule_set = $rules;
        static::extractRules();
        static::extractParams();


        var_dump(static::$rule_set);
//die;
        return true;
    }

    protected static function extractRules(): void
    {
        foreach (static::$rule_set as $rule => $value) {
            static::$rule_set[$rule] = explode('|', $value);
        }
    }

    protected static function extractParams(): void
    {
        foreach (static::$rule_set as $rule => $rules) {
            foreach ($rules as $key => $params) {
                if (Str::contains($params, ':')) {
                    static::$rule_set[$rule][$key] = explode(':', $params);
                }
            }
        }
    }
}
