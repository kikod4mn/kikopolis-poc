<?php

declare(strict_types=1);

namespace Kikopolis\App\Utility;

use Kikopolis\App\Config\Config;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Hash utility.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Hash
{
    /**
     * Hash a given string with default sha256.
     * @param string $string
     * @param string $algorithm
     * @param string $hmac_key
     * @return string
     */
    public static function getHash(string $string, string $algorithm = 'sha256', string $hmac_key = Config::STRING): string
    {
        return hash_hmac($algorithm, $string , $hmac_key);
    }

    /**
     * Compare hashed value to a non hashed value.
     * @param string $string
     * @param string $hashed_string
     * @return bool
     */
    public static function compare(string $string, string $hashed_string): bool
    {
        return hash_equals(self::getHash($string), $hashed_string);
    }
}