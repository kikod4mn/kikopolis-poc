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
    public $attributes = [];

    protected $stmt;

    protected $db = null;

    protected $fillable = [];

    protected $visible = [];

    protected $guarded = [];

    protected $hidden = [];

    /**
     * Model constructor.
     * @param array $attributes The attributes of a model Class. Will be mapped according to fillable array.
     */
    final public function __construct($attributes = [])
    {
        if (!isset($this->db)) {
            $this->db = $this->getDb();
        }
        if (method_exists(get_called_class(), '__constructor')) {
            $this->__constructor();
        }
        $this->fill($attributes);
    }

    /**
     * Database connection.
     * @return PDO|null
     */
    private function getDb()
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
