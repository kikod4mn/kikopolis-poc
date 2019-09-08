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
}