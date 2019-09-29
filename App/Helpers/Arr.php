<?php

namespace Kikopolis\App\Helpers;

defined('_KIKOPOLIS') or die('No direct script access!');

class Arr
{
    /**
     * Convert an array to a stdClass object
     *
     * @param array $array
     * @return stdClass
     */
    public static function arrayToObject($array)
    {
        // Create new stdClass object
        $object = new stdClass();
        // Use loop to assign array values into
        // stdClass object properties
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = arrayToObject($value);
            }
            $object->$key = $value;
        }
        return $object;
    }

    /**
     * Convert a stdClass object to an array
     * 
     * @param stdClass $object
     * @return array
     */
    public static function objectToArray($object)
    {
        if (is_array($object) || is_object($object)) {
            $result = array();
            foreach ($object as $key => $value) {
                $result[$key] = objectToArray($value);
            }
            return $result;
        }
        return $object;
    }

    public static function arrayFlatten(array $array): array
    {
        $return = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, static::arrayFlatten($value));
            } else {
                $return[] = $value;
            }
        }

        return $return;
    }

    public static function arrayFilter(array $array, string $callback = 'is_string', $flags = ARRAY_FILTER_USE_KEY)
    {
        $array = array_filter($array, $callback, $flags);
        return $array;
    }


    /**
     * Check that value is present in array
     * 
     * @param   mixed   $value  The value to search for in the array
     * @param   array   $data   The array to search for the value
     * @return void
     */
    public static function isIncludedInArray($key, array $data)
    {
        return in_array($key, $data);
    }

    /**
     * Check that value would not be present in array
     * 
     * @param   mixed   $value  The value to make sure is excluded in array
     * @param   array   $data   The array to search for the value
     * @return void
     */
    public static function isExcludedInArray($key, array $data)
    {
        return !in_array($key, $data);
    }


    public static function checkArrayIndexes(array $haystack, $needles)
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
