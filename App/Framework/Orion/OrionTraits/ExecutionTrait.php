<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion\OrionTraits;

use PDO;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * ExecutionTrait
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
trait ExecutionTrait
{
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

    protected function bindMany(array $data)
    {
        foreach ($data as $key => $value) {
            $this->bind($key, $value);
        }
    }

    /**
     * Prepare the query with prepared statement
     * @param $sql
     * @return self
     */
    protected function query($sql)
    {
        // If the $sql passed in is an object of QueryBuilder, use the getter to get the built query.
//        $this->stmt = $this->db->prepare(is_object($sql) ? $sql->getQuery() : $sql);
//        var_dump($sql);
        $this->stmt = $this->db->prepare($sql);
        return $this;
    }

    protected function result()
    {
        $this->execute();
        $return = $this->stmt->fetch(PDO::FETCH_OBJ);
        if (!$return) {
            return false;
        }
        return $return;
    }

    protected function resultSet()
    {
        $this->execute();
        $return = $this->stmt->fetchAll(PDO::FETCH_OBJ);
        if (!$return) {
            return false;
        }
        return $return;
    }

    protected function execute()
    {
        return $this->stmt->execute();
    }
}