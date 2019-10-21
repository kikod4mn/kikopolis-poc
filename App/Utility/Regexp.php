<?php

declare(strict_types=1);

namespace Kikopolis\App\Utility;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Regex
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Regexp
{
    /**
     * Return an array of matches with preg_match_all.
     * @param string $needle
     * @param string $haystack
     * @param int $flags
     * @return array
     */
    public static function findByRegex(string $needle, string $haystack, int $flags = PREG_SET_ORDER): array
    {
        preg_match_all($needle, $haystack, $matches, $flags);

        return $matches;
    }
}