<?php

namespace Kikopolis\Core;

use PDO;
use Kikopolis\App\Config\Config;

/**
 * The base model with PDO connection
 */

class Database
{
    protected $stmt;
    protected $db = null;

    /**
     * PDO Database connection
     * 
     * @return mixed
     */
    public function getDb()
    {
        $db = null;

        if ($db === null) {
            $error = '';
            $dsn = 'mysql:host=' . Config::DBHOST . ';dbname=' . Config::DBNAME;
            $options = [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false
            ];

            try {
                $db = new PDO($dsn, Config::DBUSER, Config::DBPASS, $options);
            } catch (PDOException $e) {
                $error = $e->getMessage();
                echo $error;
            }
        }

        return $db;
    }

    /**
     * Prepare the query with prepared statement
     * 
     * @return void
     */
    public function query($sql)
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
    public function bind($param, $value, $type = null)
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

    public function execute()
    {
        return $this->stmt->execute();
    }

    // Get single record as object
    public function result()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get result set as array of objects
    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get result set as array of objects
    public function resultClass()
    {
        $this->stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $this->execute();
        return $this->stmt->fetch();
    }

    // Get result set as array of objects
    public function resultSetArray()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get row count
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}
