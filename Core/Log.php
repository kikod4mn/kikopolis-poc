<?php

declare(strict_types=1);

namespace Kikopolis\Core;

use Kikopolis\App\Config\Config;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Log
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Log
{
    protected static $log_dir = '';

    protected static function boot($dir)
    {
        self::$log_dir = Config::getLogRoot() . $dir;
    }

    public static function create($title, $message, $dir = null)
    {
        self::boot($dir);
        if (is_object($message) || is_array($message)) {
            $message = self::processArray($message);
        }
        file_put_contents(self::$log_dir . $title . time() . ".txt", $message, LOCK_EX);
    }

    protected static function processArray($iterable)
    {
        $message = '';
        foreach ($iterable as $key => $value) {
            $message .= "{$key} - {$value}" . PHP_EOL;
        }
        return $message;
    }
}