<?php

declare(strict_types=1);

namespace Kikopolis\Core;

use Kikopolis\App\Framework\Orion\Connection\Connection;
use Kikopolis\App\Framework\Orion\QueryBuilder;
use Kikopolis\App\Helpers\Str;
use Kikopolis\App\Framework\Orion\Orion;

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

    // todo - implement id for queries
    protected $mainId = 'id';

    /**
     * Instance of the QueryBuilder class
     * @var QueryBuilder|null
     */
    protected $query_builder = null;

    /**
     * The table associated with model.
     * @var string
     */
    protected $table = '';

    /**
     * Array of attributes.
     * Filled with the attributes in the fillable array of the model.
     * @var array
     */
    public $attributes = [];
    protected $query = '';
    protected $stmt;
    protected $errors = [];
    protected $db = null;
    private $last_id;
    protected $created_at;
    protected $updated_at;

    /**
     * Model constructor.
     * @param array $attributes The attributes of a model Class. Will be mapped according to fillable array.
     * @throws \Exception
     */
    final public function __construct($attributes = [])
    {
        if (!isset($this->db)) {
            $this->db = Connection::connect();
        }
        if (method_exists(get_called_class(), '__constructor')) {
            $this->__constructor();
        }
        if ($attributes !== []) {
            $this->fill($attributes);
        }

        $this->table = $this->getCallingClassName();
        $this->query_builder = new QueryBuilder();
    }

    /**
     * Returns all results found in the specified columns for this model. Default is all columns.
     * @param int $limit
     * @param array $columns
     * @return mixed
     * @throws \Exception
     */
    final public function all(int $limit = 0, array $columns = ['*'])
    {
        $this->query = $this->query_builder->select($columns)->from($this->table)->limit($limit)->create();
        $this->query($this->query);

        return $this->resultSet();
    }

	/**
	 * Get a series of results for this model, specify $key for column and $value for a value to search.
	 * @param string $key
	 * @param string $value
	 * @param int    $limit
	 * @param array  $columns
	 * @return string
	 * @throws \Exception
	 */
    final public function get(string $key, string $value, int $limit = 0, array $columns = ['*'])
    {
        $this->query = $this->query_builder->select($columns)->from($this->table)->limit($limit)->where($key, $value)->create();
        $this->query($this->query);
        $this->bind($key, $value);
        $result = $this->resultSet();

        return $result;
    }

    /**
     * Find and return a database row with a provided key.
     * If the key is an int, it is assumed to be an id, if it contains an @ symbol, assumed to be an email.
     * If a key value is passed in, return that result.
     * @param $key
     * @return object
     * @throws \Exception
     */
    final public function find($key, string $value = '')
    {
        // Determine the key type. Prioritized are integers as id or string containing an @ sign as an email.
        // If no type is determined or it isnt the two mentioned, the entire table is searched for a match.
        $key_type = $this->whatIsKey($key);
        switch ($key_type) {
            case 'id':
                $this->query = $this->query_builder->select()->from($this->table)->where('id', $key)->create();
                $this->query($this->query);
                $this->bind('id', $key);
				$result = $this->result();
                break;
            case 'email':
                $this->query = $this->query_builder->select()->from($this->table)->where('email', $key)->create();
                $this->query($this->query);
                $this->bind('email', $key);
				$result = $this->result();
                break;
            default:
				$this->query = $this->query_builder->select()->from($this->table)->where($key, $value)->create();
				$this->query($this->query);
				$this->bind($key, $value);
				$result = $this->result();
        }

        return $result;
    }

    /**
     * Save the model to the database.
     * @param array $data
     * @return mixed $lastInsertedId
     * @throws \Exception
     */
    final protected function insert(array $data)
    {
        $insert = new $this($data);
//        $this->query_builder->setParameters($insert->attributes);
        $this->query = $this->query_builder->insert($this->table)->parameters($insert->attributes)->create();
        var_dump($this->query);
        $this->query($this->query);
        $this->bindMany($insert->attributes);
        if ($this->execute() !== true) {
            Log::create("SQL_INSERT_query", $this->query);
            Log::create("{$this->table}_attributes", $insert->attributes);
            throw new \Exception("Error creating new entry to the database.");
        }

        return $this->lastId();
    }

    /**
     * Update a model in the database.
     * @param array $data
     * @return string
     * @throws \Exception
     */
    final protected function modify(array $data)
    {
        if ($data['id'] != (int) $data['id']) {
            throw new \Exception("Invalid id!");
        }
        $update = new $this($data);
        $this->query = $this->query_builder->update($this->table)->parameters($update->attributes)->where('id', $update->attributes['id'])->create();
        $this->query($this->query);
        $this->bindMany($update->attributes);
        $result = $this->execute();
        if ($result !== true) {
            Log::create("SQL_UPDATE_query", $this->query);
            Log::create("{$this->table}_attributes", $update->attributes);
            throw new \Exception("Error updating entry {$update->attributes['id']} in the the database.");
        }
        $this->last_id = (int) $data['id'];

        return $result;
    }

    final protected function destroy($id)
    {
        $this->query = $this->query_builder->delete($this->table)->where('id', $id)->create();
        $this->query($this->query);
        $this->bind(':id', $id);
        $result = $this->execute();
        if ($result !== true) {
            Log::create("SQL_UPDATE_query", $this->query);
            Log::create("{$this->table}_attributes", $update->attributes);
            throw new \Exception("Error updating entry {$update->attributes['id']} in the the database.");
        }

        return $result;
    }

    final protected function extract()
    {

    }

    /**
     * Determine if the model uses timestamps.
     * @return mixed
     */
    final protected function hasTimeStamps()
    {
        // TODO: Implement hasTimeStamps() method.
    }

    /**
     * Set the timestamps of the model.
     * On create, set the created at and updated at.
     * On update, only set updated at.
     * @param string $type
     * @return mixed
     */
    final protected function setTimestamps($type = 'create')
    {
        if ($type === 'create') {
            $this->created_at = time();
            $this->updated_at = time();
        } elseif ($type === 'update') {
            $this->updated_at = time();
        }
        $class = get_class();
        Log::create("Error setting {$class} timestamps with {$type}. Accepted are 'update' and 'create'");
        return false;
    }

    final protected function increment()
    {

    }

    final protected function decrement()
    {

    }

    /**
     * Retrieve the last inserted or modified id from the database.
     * @return string
     */
    final public function lastId()
    {
        $id = 0;
        if (isset($this->last_id)) {
            $id = $this->last_id;
            unset($this->last_id);
        } else {
            $id = $this->db->lastInsertId();
        }
        return $id;
    }

    /**
     * Return the name of the calling model in plural.
     * @return string
     */
    private function getCallingClassName(): string
    {
        $arr = explode('\\', static::class);
        return lcfirst(end($arr)) . 's';
    }

    /**
     * Determine the incoming key type.
     * ID and Email are allowed only for dynamic search.
     * @param $key
     * @return bool|string
     */
    private function whatIsKey($key)
    {
        switch ($key) {
            case is_int($key) || is_numeric($key):
            case $key === (int) $key:
                return 'id';
            case Str::contains((string) $key, '@'):
                return 'email';
            default:
                return false;
        }
    }


}
