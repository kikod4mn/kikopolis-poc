<?php

declare(strict_types=1);

namespace Kikopolis\App\Helpers;

use Kikopolis\App\Helpers\Validate;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Numerical helper methods.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Num
{
    /**
     * Check for a value and if value is a valid integer
     * @param   mixed   $number The number to validate
     * @return  boolean
     */
    public static function isValueAnInt($number)
    {
        if (!Validate::hasValue($number)) {

            return false;
        }
        if ($number === (int) $number) {

            return true;
        } else {

            return false;
        }
    }
}