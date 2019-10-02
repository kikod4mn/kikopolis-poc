<?php declare(strict_types=1);

namespace Kikopolis\App\Helpers;

defined('_KIKOPOLIS') or die('No direct script access!');

class Num
{
    /**
     * Check for a value and if value is a valid integer
     * @param   mixed   $number The number to validate
     * @return  boolean
     */
    public static function isValueAnInt($number)
    {
        if (!static::hasValue($number)) {
            return false;
        }
        if ($number === (int) $number) {
            return true;
        } else {
            return false;
        }
    }
}