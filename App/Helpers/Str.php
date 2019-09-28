<?php

namespace Kikopolis\App\Helpers;

defined('_KIKOPOLIS') or die('No direct script access!');

class Str
{
    /**
     * Sanitize data for HTML output.
     * 
     * @param string $string
     * @param mixed $tags
     * @param string $encoding
     * @return string $string
     */
    public static function h(string $string, $tags = ENT_QUOTES, $encoding = 'UTF-8')
    {
        return htmlspecialchars($string, $tags, $encoding);
    }

    /**
     * Sanitize data for HTML output but allow html tags.
     * 
     * @param string $string
     * @param mixed $tags
     * @param string $encoding
     * @return string $string
     */
    public static function hWithHtml(string $string, $tags = ENT_QUOTES, $encoding = 'UTF-8')
    {
        return htmlspecialchars_decode(htmlspecialchars($string, $tags, $encoding));
    }

    /**
     * JSON Encode the string
     * 
     * @param string $string
     * @return string $string
     */
    public static function j(string $string)
    {
        return json_encode($string);
    }

    /**
     * Sanitize and url encode the string.
     * 
     * @param string $string
     * @return string $string
     */
    public static function u(string $string)
    {
        return filter_var($string, FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize string for forbidden chars.
     * Double protection really since twig is awesome at escaping data but still. security doesn't hurt
     * 
     * @param string $string
     * @return string $string
     */
    public static function removeForbiddenCharsFromString(string $string)
    {
        $invalid_chars = ['/', '\\', '.', ';', '<', '>', '`', '^'];
        $string = str_replace($invalid_chars, '', $string);
        return $string;
    }

    /**
     * Search a larger string for a needle or an array of needles.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return boolean
     */
    public static function contains(string $haystack, $needles)
    {
        if ($needles instanceof Traversable || is_array($needles)) {
            foreach ($needles as $needle) {
                if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                    return true;
                }
            }
        } else {
            if ($needles !== '' && mb_strpos($haystack, $needles) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate a pseudo random string.
     *
     * @param   integer   $length
     * @return  string
     */
    public static function randomString(int $length = 16)
    {
        $string = '';
        while (($len = strlen($string) < $length)) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    /**
     * Limit word count in a string to the optional specified number and append the optional parameter '...'.
     *
     * @param   string    $string
     * @param   integer   $limit    Optional word limit.
     * @param   string    $append   Option append to the end of string to signify there is more content.
     * @return  string
     */
    public static function limitWords(string $string, int $limit = 150, string $append = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);
        if (!$matches[0] || strlen($string) === strlen($matches[0])) {
            return $string;
        }
        return rtrim($matches[0]) . $append;
    }

    /**
     * Convert a string to url friendly slug format.
     *
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function slug(string $string, string $separator = '-')
    {
        // Convert underscores into separator
        $flip = $separator === '-' ? '_' : '-';
        $string = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $string);
        // Replace @ with at
        $string = str_replace('@', $separator . 'at' . $separator, $string);
        // Replace & with and
        $string = preg_replace('/\&/', $separator . 'and' . $separator, $string);
        // Replace % with percentage
        $string = preg_replace('/\%/', $separator . 'percentage' . $separator, $string);
        // Remove all chars that are not whitespace, separator, letters or numbers
        $string = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', strtolower($string));
        // Replace all whitespace and separator with single separator
        $string = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $string);
        // Return the string
        return trim($string, $separator);
    }

    /**
     * Convert a string to snake_case.
     *
     * @param   string  $string
     * @param   string  $separator   Optionally declare a different separator.
     * @return  string
     */
    public static function convertToSnakeCase(string $string, string $separator = '_')
    {
        $string = preg_replace('/\s+/u', '', ucwords($string));
        $string = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $separator, $string));
        return $string;
    }

    /**
     * Convert a string to StudlyCase.
     *
     * @param   string  $string
     * @return  string
     */
    public static function convertToStudlyCase(string $string)
    {
        $string = ucwords(str_replace(['-', '_'], ' ', $string));
        return str_replace(' ', '', $string);
    }

    /**
     * Convert a string to camelCase.
     *
     * @param   string  $string
     * @return  string
     */
    public static function convertToCamelCase(string $string)
    {
        return lcfirst(static::convertToStudlyCase($string));
    }

    /**
     * Check string length as greater than
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * 
     * hasLengthGreaterThan($data, $min);
     * 
     * @param   string  $string   The string to test for length
     * @param   int     $min    The minimum length required as an integer
     * @return boolean
     */
    public static function hasLengthGreaterThan(string $string, int $min)
    {
        $length = mb_strlen(trim($string));

        return $length > $min;
    }

    /**
     * Check string length as less than
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * 
     * hasLengthGreaterThan($data, $min);
     * 
     * @param   string  $string   The string to test for length
     * @param   int     $max    The minimum length required as an integer
     * @return boolean
     */
    public static function hasLengthLessThan(string $string, int $max)
    {
        $length = mb_strlen(trim($string));

        return $length < $max;
    }

    /**
     * Check string length to be exact
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * 
     * hasLengthExact($data, $exact);
     * 
     * @param   string  $string   The string to test for length
     * @param   int     $exact  The length to match as integer
     * @return boolean
     */
    public static function hasLengthExact(string $string, int $exact)
    {
        $length = mb_strlen(trim($string));

        return $length == $exact;
    }

    /**
     * Check string length, combining multiple methods
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * Combines hasLengthGreaterThan, hasLengthLessThan and hasLengthExact methods
     * 
     * @param   string  $string   The string to test for length
     * @param   int     $min    OPTIONAL The minimum length to match
     * @param   int     $max    OPTIONAL The maximum length to match
     * @param   int     $exact  OPTIONAL The exact length to match
     * @return boolean
     */
    public static function hasLength(string $string, array $options)
    {
        if (isset($options['min']) && !static::hasLengthGreaterThan($string, $options['min'] - 1)) {

            return false;
        } elseif (isset($options['max']) && !static::hasLengthLessThan($string, $options['max'] + 1)) {

            return false;
        } elseif (isset($options['exact']) && !static::hasLengthExact($string, $options['exact'])) {

            return false;
        } else {

            return true;
        }
    }

    public static function parseDotSyntax(string $string)
    {
        return static::contains($string, '.') ? explode('.', $string) : [$string];
    }

    public static function parseSlashSyntax(string $string)
    {
        return static::contains($string, '/') ? explode('/', $string) : [$string];
    }

    public static function parseCallback(string $string)
    {
        return static::contains($string, '@') ? explode('@', $string, 2) : [$string];
    }
}
