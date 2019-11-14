<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion\Connection;

use Kikopolis\App\Config\Config;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Connection
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Connection
{
    public static function connect()
    {
        if (Config::DB_TYPE === 'mysql') {
            return MySQL::create();
        }

        throw new \Exception(sprintf("Cannot create a database connection. DB type [%s] is not defined in the class [%s] as a valid 
                                                connection and is therefor not supported by this framework.",
                                            Config::DB_TYPE, Connection::class));
    }
}