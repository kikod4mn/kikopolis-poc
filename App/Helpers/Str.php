<?php

declare(strict_types=1);

namespace Kikopolis\App\Helpers;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Utility\Validate;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * String helper methods.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Str
{
    /**
     * Sanitize data for HTML output.
     * @param string $string
     * @param int $flags
     * @param string $encoding
     * @return string
     */
    public static function h(string $string, int $flags = ENT_QUOTES, $encoding = 'UTF-8'): string
    {
        return htmlspecialchars($string, $flags, $encoding);
    }

    /**
     * Sanitize data for HTML output but allow html tags.
     * @param string $string
     * @param mixed $flags
     * @param string $encoding
     * @return string
     */
    public static function hWithHtml(string $string, int $flags = ENT_QUOTES, $encoding = 'UTF-8'): string
    {
    	// @todo : write whitelist of html tags
        return htmlspecialchars($string, $flags, $encoding);
    }

    /**
     * JSON Encode the string
     * @param string $string
     * @return string
     */
    public static function j(string $string): string
    {
        return json_encode($string);
    }

    /**
     * Sanitize and url encode the string.
     * @param string $string
     * @return string
     */
    public static function u(string $string): string
    {
        return filter_var($string, FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize string for forbidden chars.
     * @param string $string String to filter.
     * @param array $chars Array of characters to remove.
     * @return string
     */
    public static function forbiddenChars(string $string, array $chars = ['/', '\\', '.', ';', '<', '>', '`', '^', '"', '\'']): string
    {
        $string = str_replace($chars, '', $string);

        return $string;
    }

    /**
     * Trim the string of empty spaces and check that it is not empty.
     * @param string $string
     * @return bool
     */
    public static function hasValue(string $string)
    {
        $string = trim($string);

        return isset($string);
    }

    /**
     * Search a larger string for a needle or an array of needles.
     * @param string $haystack
     * @param string|array $needles
     * @return boolean
     */
    public static function contains(string $haystack, $needles): bool
    {
        if ($needles instanceof \Traversable || is_array($needles)) {
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
     * @param integer $length
     * @throws \Exception random_bytes throws \Exception if no sufficient entropy was gathered.
     * @return  string
     */
    public static function random(int $length = 16): string
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
     * Check if string is alphabetical only, no numbers or extra characters allowed
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * @param   string  $string
     * @param array $extra_chars Extra chars to allow in the string. Removed before checking. DO NOT ALLOW < or > !!
     * @return bool
     */
    public static function isAlphabetic(string $string, array $extra_chars = [' ', "'", '-', '_', '.', '!', '?']): bool
    {
        if (Validate::hasValue($string)) {
            $string = str_replace($extra_chars, '', $string);
            // Ctype returns false on empty string, this is to avoid false positive
            if (strlen($string) === 0) {

                return true;
            }

            return ctype_alpha($string);
        }

        return false;
    }

    /**
     * Check if string is alphanumerical only, only letters and numbers allowed
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * @param string $string
     * @param array $extra_chars Extra chars to allow in the string. Removed before checking. DO NOT ALLOW < or > !!
     * @return bool
     */
    public static function isAlphaNumeric(string $string, array $extra_chars = [' ', "'", '-', '_', '.', '!', '?']): bool
    {
        if (Validate::hasValue($string)) {
            $string = str_replace($extra_chars, '', $string);
            // Ctype returns false on empty string, this is to avoid false positive
            if (strlen($string) === 0) {

                return true;
            }

            return ctype_alnum($string);
        }

        return false;
    }

    /**
     * Verify the string is in a valid date format.
     * @param string $string
     * @return bool
     */
    public static function validDate(string $string): bool
    {
        return (bool) strtotime($string);
    }

    /**
     * Check if the string is a valid phone number.
     * @param $string
     * @return bool
     */
    public static function phoneNumber(string $string): bool
    {
        $array = ['-', '.', ' ', '_', '+'];
        $string = str_replace($string, '', $array);

        return (bool) preg_match('/^[0-9]{10}+$/', $string);
    }

    /**
     * Check if string matches a valid email address format
     * Uses trim to remove empty spaces from the beginning and end of the string
     * @param string $email
     * @return bool
     */
    public static function email(string $email): bool
    {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }

    /**
     * Limit word count in a string to the optional specified number and append the optional parameter '...'.
     * @param   string    $string
     * @param   integer   $limit    Optional word limit.
     * @param   string    $append   Option append to the end of string to signify there is more content.
     * @return  string
     */
    public static function limitWords(string $string, int $limit = 150, string $append = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);
        if (!$matches[0] || strlen($string) === strlen($matches[0])) {

            return $string;
        }

        return rtrim($matches[0]) . $append;
    }

    /**
     * Convert a string to url friendly slug format.
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function slug(string $string, string $separator = '-'): string
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

        return trim($string, $separator);
    }

    /**
     * Convert a string to snake_case.
     * @param   string  $string
     * @param   string  $separator   Optionally declare a different separator.
     * @return  string
     */
    public static function snake(string $string, string $separator = '_'): string
    {
        $string = preg_replace('/\s+/u', '', ucwords($string));
        $string = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $separator, $string));

        return $string;
    }

    /**
     * Convert a string to StudlyCase.
     * @param   string  $string
     * @return  string
     */
    public static function studly(string $string): string
    {
        $string = ucwords(str_replace(['-', '_'], ' ', $string));

        return str_replace(' ', '', $string);
    }

    /**
     * Convert a string to camelCase.
     * @param   string  $string
     * @return  string
     */
    public static function camel(string $string): string
    {
        return lcfirst(static::studly($string));
    }

    public static function toText(string $string): string
    {
        $string = ucfirst(strtolower($string));

        return str_replace(['-', '_', '.'], ' ', $string);
    }

    /**
     * Check string length as greater than
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * @param   string  $string   The string to test for length
     * @param   int     $min    The minimum length required as an integer
     * @return boolean
     */
    public static function hasLengthGreater(string $string, int $min): bool
    {
        return mb_strlen(trim($string)) > $min;
    }

    /**
     * Check string length as less than
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * @param   string  $string   The string to test for length
     * @param   int     $max    The minimum length required as an integer
     * @return boolean
     */
    public static function hasLengthLess(string $string, int $max): bool
    {
        return mb_strlen(trim($string)) < $max;
    }

    /**
     * Check string length to be exact
     * Uses trim() to remove empty spaces from the beginning and end of the string.
     * @param   string  $string   The string to test for length
     * @param   int     $exact  The length to match as integer
     * @return boolean
     */
    public static function hasLengthExact(string $string, int $exact): bool
    {
        return (int) mb_strlen(trim($string)) === $exact;
    }

    /**
     * Check string length, combining multiple methods
     * Uses trim() to remove empty spaces from the beginning and end of the string
     * Combines hasLengthGreaterThan, hasLengthLessThan and hasLengthExact methods
     * @param string $string The string to test for length
     * @param array $options
     * @return bool
     */
    public static function hasLength(string $string, array $options): bool
    {
        if (isset($options['min']) && !static::hasLengthGreater($string, $options['min'] - 1)) {

            return false;
        } elseif (isset($options['max']) && !static::hasLengthLess($string, $options['max'] + 1)) {

            return false;
        } elseif (isset($options['exact']) && !static::hasLengthExact($string, $options['exact'])) {

            return false;
        } else {

            return true;
        }
    }

    /**
     * Explode a string with dot syntax to an array.
     * @param string $string
     * @return array
     */
    public static function dot(string $string): array
    {
        return static::contains($string, '.') ? explode('.', $string) : [$string];
    }

	/**
	 * Explode a string with dot syntax to an array.
	 * @param string $string
	 * @return array
	 */
	public static function comma(string $string): array
	{
		return static::contains($string, ',') ? explode(', ', $string) : [$string];
	}

    /**
     * Explode a string with slash syntax to an array.
     * @param string $string
     * @return array
     */
    public static function slash(string $string): array
    {
        return static::contains($string, '/') ? explode('/', $string) : [$string];
    }

    /**
     * Explode a string with @ syntax to an array.
     * @param string $string
     * @return array
     */
    public static function callback(string $string): array
    {
        return static::contains($string, '@') ? explode('@', $string, 2) : [$string];
    }

	public static function removeFileFromPath(string $file_path):  string
	{
		$array = explode('/', implode('/', explode('\\', $file_path)));
		array_pop($array);
		return implode(DIRECTORY_SEPARATOR, $array);
    }

	public static function correctFilePath(string $path): string
	{
		$array = explode('/', implode('/', explode('\\', $path)));
		return implode(DIRECTORY_SEPARATOR, $array);
    }
}
