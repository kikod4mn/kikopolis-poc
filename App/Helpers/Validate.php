<?php

namespace Kikopolis\App\Helpers;

defined('_KIKOPOLIS') or die('No direct script access!');

class Validate
{
    /**
     * Check data presence
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * 
     * @param   mixed  $data   The variable to test for data
     * @return void
     */
    public static function hasValue($data)
    {
        $data = trim($data);
        return isset($data);
    }

    /**
     * Check for a value and if value is a valid integer
     * 
     * @param   mixed   $number The number to validate
     * @return void
     */
    public static function isValueAnInt($number)
    {
        if (static::hasValue($number)) {
            if ($number == (int) $number) {
                return true;
            } else {
                return false;
            }
        }
    }
}
