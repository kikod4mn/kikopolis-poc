<?php

namespace Kikopolis\Core\Orion;

use PDO;
use Kikopolis\App\Config\Config;
use Kikopolis\Core\Orion\Orion;

/**
 * The base model with PDO connection
 */

class Model extends Orion
{
    protected $stmt;

    protected $db = null;

    final public function __construct(array $attributes = [])
    {
        $this->db = $this->getDb();
        if (method_exists(get_called_class(), '__constructor')) {
            $this->__constructor();
        }
        $this->fill($attributes);
    }

    /**
     * PDO Database connection
     * 
     * @return mixed
     */
    protected function getDb()
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
}
