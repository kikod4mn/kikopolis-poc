<?php declare(strict_types=1);

namespace Kikopolis\Core\Orion;

use Kikopolis\App\Helpers\Str;
use PDO;
use PDOException;
use Kikopolis\App\Config\Config;
use Kikopolis\Core\Orion\OrionTraits\ManagePropertiesTrait;
use Kikopolis\Core\Orion\OrionTraits\ManageQueryTempTrait;

defined('_KIKOPOLIS') or die('No direct script access!');

abstract class Orion
{
    use ManagePropertiesTrait, ManageQueryTempTrait;

    public $attributes = [];

    protected $stmt;

    protected $errors = [];

    protected $db = null;

    public $lastInsertedId = 0;

    /**
     * Database connection.
     * @return PDO|null
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

    /**
     * Returns all results found in the specified columns.
     *
     * @param int $limit
     * @param array $columns
     * @return object
     * @throws \Exception
     */
    final public function get(int $limit = 0, array $columns = ['*'])
    {
        $return_obj = (object) [];
        $cols = implode(',', $columns);
        $class = $this->getCallingClassName();
        $sql = "SELECT {$cols} FROM {$class}s";

        if ($limit !== 0) {
            $sql .= " LIMIT {$limit}";
        }

        $this->query($sql);

        $raw = $this->resultSet();
        foreach ($raw as $single) {
            foreach ($single as $col => $value) {
                $model = new $this($single);
            }
            $name = random_int(1111, 9999).rand(0000, 9999);

            $return_obj->{$name} = $this->show($model->attributes);
        }
        return $return_obj;
    }

    final public function find($key)
    {
        $return_obj = (object) [];
        $class = $this->getCallingClassName();
//        if ($key === $this->whatIsKey($key)) {
//            $this->query('SELECT * FROM ' . $class . 's WHERE * LIKE '.$key);
//        }
        $this->query("DESCRIBE {$class}s");
        $results = $this->resultSet();

        $columns = $this->buildValuesForSearch($results);

        $key_type = $this->whatIsKey($key);

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

        $single = $this->result();

//        $model = new $this($single);
        $model = $this->show($single);
        var_dump($model);
        return $model;
//        return $model->attributes;
//        $model->attributes['password_hash'] = '';
//        $name = random_int(1111, 9999).rand(0000, 9999);
//        $return_obj->{$name} = $model->attributes;
//        return $return_obj;
    }

    private function whatIsKey($key)
    {
        switch ($key) {
            case is_int($key) || is_numeric($key):
                return 'id';
            case Str::contains($key, '@'):
                return 'email';
            case is_string($key):
                return $key;
        }
    }

    final protected function save(array $data) {
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

    final public function getLastId() {
        return $this->db->lastInsertId();
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
