<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion\MySQL;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * MysqlFunc
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class MysqlFunc
{
    protected static $db = null;

    public static function bind(\PDO $db, $parameters)
    {
        self::$db = $db;

        foreach ($parameters as $parameter) {
            if (is_null($parameter['type'])) {
                switch (true) {
                    case is_int($parameter['value']):
                        $parameter['type'] = PDO::PARAM_INT;
                        break;
                    case is_bool($parameter['value']):
                        $parameter['type'] = PDO::PARAM_BOOL;
                        break;
                    case is_null($parameter['value']):
                        $parameter['type'] = PDO::PARAM_NULL;
                        break;
                    default:
                        $parameter['type'] = PDO::PARAM_STR;
                }
            }
            var_dump(self::$db->stmt);
            self::$db->stmt->bindParam($parameter['key'], $parameter['value'], $parameter['type']);
        }

        return self::getDb();
    }

    public static function getDb()
    {
        return isset(self::$db) ? self::$db : false;
    }
}