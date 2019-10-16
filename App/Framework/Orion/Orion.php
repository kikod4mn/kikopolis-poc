<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Framework\Orion\OrionTraits\PropertiesTrait;
use Kikopolis\App\Framework\Orion\OrionTraits\QueryTemporaryTrait;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Orion database ORM.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

abstract class Orion
{
    use PropertiesTrait, QueryTemporaryTrait;

    public $attributes = [];

    protected $stmt;

    protected $errors = [];

    protected $db = null;

    /**
     * Database connection.
     * @return \PDO|null
     */
    protected function getDb()
    {
        $db = null;

        if ($db === null) {
            $error = '';
            $dsn = 'mysql:host=' . Config::DBHOST . ';dbname=' . Config::DBNAME;
            $options = [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES   => false
            ];

            try {
                $db = new \PDO($dsn, Config::DBUSER, Config::DBPASS, $options);
            } catch (\PDOException $e) {
                $error = $e->getMessage();
                echo $error;
            }
        }

        return $db;
    }



    protected function getCallingClassName()
    {
        $arr = explode('\\', static::class);
        return lcfirst(end($arr));
    }

    protected function buildValuesForInsert($save) {
        $values = '';
        foreach ($save->attributes as $key => $value) {
            $values .= " {$key} = :{$key},";
        }

        return trim($values, ', ');
    }

    protected function buildValuesForSearch($columns) {
        $values = '';
        foreach ($columns as $key => $value) {
            $values .= " {$value->Field}, ";
        }

        return trim($values, ', ');
    }
}
