<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion\Connection;

use Kikopolis\App\Config\Config;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * MySQL
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class MySQL
{
    public static function create()
    {
        return self::createPdoConn();
    }

    /**
     * Database connection.
     * @return \PDO|null
     */
    protected static function createPdoConn()
    {
        $db = null;
        if ($db === null) {
            $error = '';
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME;
            $options = [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES   => false
            ];
            try {
                $db = new \PDO($dsn, Config::DB_USER, Config::DB_PASS, $options);
            } catch (\PDOException $e) {
                $error = $e->getMessage();
                echo $error;
            }
        }

        return $db;
    }
}