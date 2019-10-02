<?php

declare(strict_types=1);

namespace Kikopolis\App\Helpers;

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
}
