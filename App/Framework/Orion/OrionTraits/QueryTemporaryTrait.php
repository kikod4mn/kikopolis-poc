<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion\OrionTraits;

use Kikopolis\App\Helpers\Str;
use PDO;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * OrionTrait
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

trait QueryTemporaryTrait
{
    /**
     * Prepare the query with prepared statement
     * @return self
     */
    protected function query($sql)
    {
        // If the $sql passed in is an object of QueryBuilder, use the getter to get the built query.
//        $this->stmt = $this->db->prepare(is_object($sql) ? $sql->getQuery() : $sql);
        $this->stmt = $this->db->prepare($sql);
        return $this;
    }

    /**
     * Bind the values in the query automatically depending on type
     * @param string $param The Database table field name in the PDO prepared statement
     * @param mixed $value The value to be added to the database
     * @param null $type The type of value, to be determined in this function
     */
    protected function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindParam($param, $value, $type);
    }

    protected function describe($table)
    {
        $this->query("DESCRIBE {$table}");
        return $this->resultSet();
    }

    protected function execute()
    {
        return $this->stmt->execute();
    }

    // Get single record as object
    protected function result()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get result set as array of objects
    protected function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get result set as array of objects
    protected function resultClass()
    {
        $this->stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $this->execute();
        return $this->stmt->fetch();
    }

    protected function bindAndExecute(array $params)
    {
        foreach ($params as $key => $value) {
            $this->bind($key, $value);
        }
        $this->execute();
    }

    // Get result set as array of objects
    protected function resultSetClass()
    {
        $this->stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Get result set as array of objects
    protected function resultSetArray()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get row count
    protected function rowCount()
    {
        return $this->stmt->rowCount();
    }

    protected function getLastId() {
        return $this->db->lastInsertId();
    }

    protected function whatIsKey($key)
    {
        switch ($key) {
            case is_int($key) || is_numeric($key):
            case $key == (int) $key:
                return 'id';
            case Str::contains((string) $key, '@'):
                return 'email';
            case is_string($key):
                return $key;
        }
    }
}
