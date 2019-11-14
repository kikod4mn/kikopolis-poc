<?php

declare(strict_types=1);

namespace Kikopolis\App\Helpers;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Array and object helper methods.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Arr
{
    /**
     * Convert an array to an object
     * @param $array
     * @return object
     */
    public static function arrToObj($array): object
    {
        // If the passed in variable is an object, return it immediately without going further.
        if (is_object($array)) {
            return $array;
        }
        // Create new empty object.
        $object = (object) [];
        // Recursively go over all array keys and assign to object properties.
        foreach ($array as $key => $value) {
            // If the value is an array, send it on another run through this function.
            if (is_array($value)) {
                $value = static::arrToObj($value);
            }
            $object->$key = $value;
        }

        return $object;
    }

    /**
     * Convert a stdClass object to an array
     * @param $object
     * @return array
     */
    public static function objToArr($object): array
    {
        // Create new empty array.
        $array = [];
        // Recursively go over object properties and assign to array values.
        foreach ($object as $key => $value) {
            // If the value is an object, send it on another run through this function.
            if(is_object($value)) {
                $value = static::objToArr($value);
            }
            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * Flatten a multidimensional array. Will return flattened with numerical keys.
     * @param array $array
     * @return array
     */
    public static function arrayFlatten(array $array): array
    {
        // Create empty new return array
        $return = [];
        // Loop through all values recursively.
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, static::arrayFlatten($value));
            } else {
                $return[] = $value;
            }
        }

        return $return;
    }

    /**
     * Filter an array with a callback function.
     * @param array     $array      The array to filter.
     * @param string    $callback   Callback function to filter with, default 'is_string'.
     * @param int       $flags      Flags to use when filtering, default ARRAY_FILTER_USE_KEY.
     * @return array
     */
    public static function arrayFilter(array $array, string $callback = 'is_string', $flags = ARRAY_FILTER_USE_KEY): array
    {
        $array = array_filter($array, $callback, $flags);

        return $array;
    }

    /**
     * Check for allowed parameters in a Post array
     * @param   array   $post             The $_POST super global array
     * @param   array   $allowed_params    An array of allowed parameters
     * @return  array   $post   A filtered version of POST
     */
    public static function filterArray(array $post, array $allowed_params = []): array
    {
        $filtered_array = [];
        foreach ($allowed_params as $field_value) {
            if (isset($post[$field_value])) {
                $filtered_array[$field_value] = $post[$field_value];
            } else {
                $filtered_array[$field_value] = null;
            }
        }

        return $filtered_array;
    }

    /**
     * Remove non allowed params from array.
     * @param array $array
     * @param array $not_allowed_params
     * @return array
     */
    public static function filteredArrayMethods(array $array, array $not_allowed_params = []): array
    {
        $filtered_array = [];
        foreach ($array as $key => $value) {
            if (!in_array($value, $not_allowed_params)) {
                $value = str_replace('Action', '', $value);
                $filtered_array[] = $value;
            }
        }

        return $filtered_array;
    }


    /**
     * Check if a value is present in array.
     * @param $needle
     * @param array $array
     * @param bool $strict True to also check if needle and array value types match.
     * @return bool
     */
    public static function isIncludedInArray($needle, array $array, bool $strict = false): bool
    {
        return in_array($needle, $array, $strict);
    }

    /**
     * Check if a value would not be present in array.
     *
     * @param $needle
     * @param array $array
     * @param bool $strict True to also check if needle and array value types match.
     * @return bool
     */
    public static function isExcludedInArray($needle, array $array, bool $strict = false): bool
    {
        return !in_array($needle, $array, $strict);
    }

    /**
     * Check array keys for a specified key or an array of keys.
     * @param array $haystack
     * @param $needles
     * @return bool
     */
    public static function checkArrayIndexes($needles, array $haystack): bool
    {
        if (is_array($needles)) {
            foreach ($needles as $needle) {

                return in_array($needle, $haystack);
            }
        } else {

            return in_array($needles, $haystack);
        }
    }
}
