<?php

declare(strict_types=1);

namespace Kikopolis\Core\Orion;

use Kikopolis\Core\Orion\OrionInterfaces\ModelInterface;
use PDO;
use Kikopolis\App\Config\Config;
use Kikopolis\Core\Orion\Orion;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * The base database Model.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Model extends Orion
{
    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

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
        if ($attributes !== []) {
            $this->fill($attributes);
        }
    }

    /**
     * Returns all results found in the specified columns. Default is all columns.
     * @param int $limit
     * @param array $columns
     * @return object
     * @throws \Exception
     */
    final public function get(int $limit = 0, array $columns = ['*'])
    {
        // Init new object to fill with models.
        $object = (object) [];
        $cols = implode(',', $columns);
        $class = $this->getCallingClassName();
        $sql = "SELECT {$cols} FROM {$class}s";
        // If limit is sent in, add it to the end of the SQL query.
        if ($limit !== 0) {
            $sql .= " LIMIT {$limit}";
        }

        $this->query($sql);

        $result = $this->resultSet();

        foreach ($result as $key => $value) {
            $object->$key = $this->show($value);
        }

        return $object;
    }

    /**
     * Find and return a database row with a provided key.
     * If the key is an int, it is assumed to be an id, if it contains an @ symbol, assumed to be an email.
     * If it is something else, a full database search is performed and the first matching result is returned.
     * @param $key
     * @return object
     */
    final public function find($key)
    {
        // Resolve the class name that is using this method and use that as a plural for the table name.
        $class = $this->getCallingClassName();
        // Get all the tables columns.
        $this->query("DESCRIBE {$class}s");
        $results = $this->resultSet();
        // Build the string for the query with all table columns.
        $columns = $this->buildValuesForSearch($results);
        // Determine the key type.
        $key_type = $this->whatIsKey($key);
        // With key type determined, switch to build the appropriate query.
        switch ($key_type) {
            case 'id':
                $this->query('SELECT * FROM ' . $class . 's WHERE id = '.$key);
                break;
            case 'email':
                $this->query('SELECT * FROM ' . $class . 's WHERE email = '.$key);
                break;
            default:
                $this->query("SELECT * FROM {$class}s WHERE CONCAT_WS('', {$columns}) LIKE '%{$key}%'");
                break;
        }
        // Store the row in a temporary variable.
        $single = $this->result();
        // Filter the properties and remove hidden.
        $model = $this->show($single);

        return $model;
    }

    /**
     * Save the model to the database.
     * @param array $data
     * @return mixed $lastInsertedId
     */
    final public function save(array $data) {
        $save = new $this($data);

        $class = $this->getCallingClassName();

        $bindings = $this->buildValuesForInsert($save);

        $this->query('INSERT INTO '. $class . 's SET ' . $bindings);

        foreach ($save->attributes as $key => $value) {
            $this->bind(':' . $key, $value);
        }
        $this->execute();

        return $this->getLastId();
    }

}
