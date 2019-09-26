<?php

namespace Kikopolis\App\Helpers;

defined('_KIKOPOLIS') or die('No direct script access!');

class FileHelper
{
    /**
     * Return the filesize in human readable format
     * Kilo, Mega, Giga and Tera units available
     *
     * @param string|int $bytes The size of the file
     * @param integer $decimals How many decimal points to have on the final number
     * @return string
     */
    public static function getHumanFileSize($bytes, $decimals = 2)
    {
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor > 0) $sz = 'KMGT';
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
    }

    /**
     * Scan a directory contents into an array
     *
     * @param string $directory
     * @return array
     */
    public static function scanDirContents(string $directory)
    {
        $result = array();
        $cdir = scandir($directory);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($directory . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = scanDirContents($directory . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }
}