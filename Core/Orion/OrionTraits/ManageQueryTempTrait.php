<?php

namespace Kikopolis\Core\Orion\OrionTraits;

use PDO;

trait ManageQueryTempTrait
{
    /**
     * Prepare the query with prepared statement
     * 
     * @return void
     */
    protected function query($sql)
    {
        $this->stmt = $this->db->prepare($sql);
    }

    /**
     * Bind the values in the query automatically depending on type
     * 
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
}
