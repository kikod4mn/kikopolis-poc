<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Utility\Validate;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Orion DB Migrator.
 * @todo Only for MySQL at the moment. Implement others later.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Migrator
{
    public function example()
    {
        $migrate = new Migrator();
        $migrate->table('posts');
        $migrate->id();
        $migrate->field('user_id')->bigInt()->foreignKey('users', 'id')->finish();
        $migrate->field('user_email')->bigInt()->foreignKey('users', 'email')->finish();
        $migrate->field('title')->varchar()->finish();
        $migrate->field('slug')->varchar()->unique()->finish();
        $migrate->field('body')->text()->finish();
        $migrate->timestamps();
        $migrate->create();

        $migrate->table('users');
        $migrate->id();
        $migrate->field('name')->varchar()->finish();
        $migrate->field('email')->varchar()->unique()->finish();
        $migrate->field('password')->varchar()->finish();
        $migrate->field('password_reset')->varchar()->null()->finish();
        $migrate->field('password_reset_expires')->varchar()->null()->finish();
        $migrate->timestamps();
        $migrate->create();
        die;
    }

    private $query = '';
    private $engine = '';
    private $char_set = '';
    private $table = '';
    private $id = 0;
    private $id_type = 'bigint';
    private $id_size = 20;
    private $id_params = [];
    private $current = [];
    private $finished = [];
    private $use_timestamps = false;
    private $timestamps = [];
    private $foreign_keys = [];
    private $use_foreign_key = false;

    /**
     * Migrator constructor.
     */
    public function __construct()
    {
        $this->query = '';
        $this->engine = Config::DB_ENGINE;
        $this->char_set = Config::DB_CHARSET;
    }

    /**
     * Set the name of the new table to create.
     * @param $table string
     */
    public function table(string $table)
    {
        $this->table = $table;
    }

    /**
     * Set an id column on the table.
     * Default is bigint(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT
     * @param string $type
     * @param int $size
     * @param array $params
     */
    public function id(string $type = 'bigint', int $size = 20, array $params = ['UNSIGNED', 'NOT NULL', 'PRIMARY KEY', 'AUTO_INCREMENT'])
    {
        $this->id = true;
        $this->id_type = $type;
        $this->id_size = $size;
        $this->id_params = $params;
    }

    /**
     * Init a new field declaration. Chain more methods to define the properties and invoke finish() to finalize the current field.
     * @param string $name
     * @return $this
     */
    public function field(string $name): self
    {
        $this->current = [
            'name' => $name,
            'type' => null,
            'size' => null,
            'null' => 'NOT NULL'
        ];

        return $this;
    }

    public function bool()
    {
        $this->current['type'] = 'int';
        $this->current['size'] = 1;
        $this->current['params'] = [
            'DEFAULT 0'
        ];

        return $this;
    }

    /**
     * Set current field to an integer.
     * @param int $length
     * @return $this
     */
    public function int(int $length = 11): self
    {
        $this->current['type'] = 'int';
        $this->current['size'] = $length;

        return $this;
    }

    /**
     * Set current field to a big integer.
     * @param int $length
     * @return $this
     */
    public function bigInt($length = 20): self
    {
        $this->current['type'] = 'bigint';
        $this->current['size'] = $length;

        return $this;
    }

    /**
     * Set current field to a medium text type.
     * @return $this
     */
    public function text(): self
    {
        $this->current['type'] = 'mediumtext';
        $this->current['size'] = null;

        return $this;
    }

    /**
     * Set current field to a longtext type.
     * @return $this
     */
    public function longtext(): self
    {
        $this->current['type'] = 'longtext';
        $this->current['size'] = null;

        return $this;
    }

    /**
     * Set the current field to a varchar type.
     * @param int $length
     * @return $this
     */
    public function varchar($length = 255): self
    {
        $this->current['type'] = 'varchar';
        $this->current['size'] = $length;

        return $this;
    }

    /**
     * Set the current table to use timestamp fields created_at and updated_at.
     */
    public function timestamps()
    {
        $this->timestamps[] = [
            'name' => 'created_at',
            'type' => 'datetime',
            'size' => '',
            'null' => 'NOT NULL',
            'params' => [
                'DEFAULT CURRENT_TIMESTAMP'
            ]
        ];
        $this->timestamps[] = [
            'name' => 'updated_at',
            'type' => 'datetime',
            'size' => '',
            'null' => 'NOT NULL',
            'params' => [
                'DEFAULT CURRENT_TIMESTAMP',
                'ON UPDATE CURRENT_TIMESTAMP'
            ]
        ];
        $this->use_timestamps = true;
    }

    /**
     * Set current field to a default null.
     * @return $this
     */
    public function null(): self
    {
        $this->current['null'] = 'DEFAULT NULL';
        return $this;
    }

    /**
     * Set the current field as a unique key.
     * @return $this
     */
    public function unique(): self
    {
        $this->current['params'][] = 'UNIQUE KEY';
        return $this;
    }

    /**
     * Set parameters for the current field. Separate into an array with each argument as a separate entry.
     * Do not specify an index or use numeric indexes.
     * @param array $params
     * @return void
     */
    public function params(array $params = []): void
    {
        foreach ($params as $param) {
            $this->current['params'][] = $param;
        }
    }

    /**
     * Define a foreign key relationship.
     * @param string $table
     * @param string $field
     * @param string $update
     * @param string $delete
     * @return $this
     */
    public function foreignKey(string $table, string $field, $update = 'CASCADE', $delete = 'CASCADE'): self
    {
        $this->foreign_keys[] = "FOREIGN KEY ({$this->current['name']}) REFERENCES {$table}({$field}) ON UPDATE {$update} ON DELETE {$delete}";
        $this->use_foreign_key = true;

        return $this;
    }

    /**
     * Call this method to finalize adding a field and add it to the array of finished fields.
     * @return void
     */
    public function finish()
    {
        $this->finished[] = $this->current;
        unset($this->current);
    }

    /**
     * Finalize the current table creation query and return it.
     * @return string
     */
    public function create()
    {
        $this->initializeNewTable();
        $this->createFinishedFields();
        $this->createTimeStamps();
        $this->createForeignKeys();
        $this->finalizeQuery();
//        echo "{$this->query}<br>";
        $this->endQueryGen();

        return $this->query;
    }

    /**
     * Initialize a new table creation.
     * Check for a defined table name, if none is found then error is shown.
     * Also adds the id field if one is specified for the table.
     * @return void
     */
    private function initializeNewTable(): void
    {
        // Reset query to default.
        $this->query = '';
        // If no table name, die with error.
        if (!Validate::hasValue($this->table)) {
            die("Table name cannot be empty. Specify a name for the new table.");
        }
        // Init a new query with table name and append id field to the query if it has been defined.
        $this->query = "CREATE TABLE `{$this->table}` (";
        if ($this->id === true) {
            $this->query .= "`id` {$this->id_type}({$this->id_size})";
            foreach ($this->id_params as $param) {
                $this->query .= " {$param}";
            }
        }
    }

    /**
     * Write timestamps into the table creation query.
     * @return void
     */
    private function createTimeStamps(): void
    {
        if ($this->use_timestamps === true) {
            foreach ($this->timestamps as $timestamp) {
                $this->createField($timestamp);
            }
        }
    }

    /**
     * Send all finished fields individually to the createField method for adding into the query.
     * @return void
     */
    private function createFinishedFields(): void
    {
        foreach ($this->finished as $field) {
            $this->createField($field);
        }
    }

    /**
     * Create individual fields from set values.
     * Name and Type are mandatory. Refer to MySQL syntax for your server to find out if a field is required to have
     * a maximum value as some fields may differ slightly.
     * If a size is set for the field it is added with params as well being optional.
     * NOT NULL is added as default. If a null field is required then chain the null method into your field method chain.
     * @param array $field
     * @return void
     */
    private function createField(array $field): void
    {
        $this->query .= ", `{$field['name']}` {$field['type']}";
        if ($field['size']) {
            $this->query .= "({$field['size']})";
        }
        $this->query .= " {$field['null']}";
        if ($field['params'] !== []) {
            foreach ($field['params'] as $param) {
                $this->query .= " {$param}";
            }
        }
    }

    /**
     * Loop through all specified foreign key relationships and add them to the query.
     * @return void
     */
    private function createForeignKeys(): void
    {
        if ($this->use_foreign_key === true) {
            foreach ($this->foreign_keys as $key) {
                $this->query .= ", $key";
            }
        }
    }

    /**
     * End the query generation and reset all class properties to default values.
     * @return void
     */
    private function endQueryGen(): void
    {
        $this->table = '';
        $this->id = 0;
        $this->id_type = 'bigint';
        $this->id_size = 20;
        $this->id_params = [];
        $this->current = [];
        $this->finished = [];
        $this->use_timestamps = false;
        $this->timestamps = [];
        $this->foreign_keys = [];
        $this->use_foreign_key = false;
    }

    /**
     * Finalize the generation query by appending the engine and charset to the string.
     * @return void
     */
    private function finalizeQuery(): void
    {
        $this->query .= ") ENGINE={$this->engine} DEFAULT CHARSET={$this->char_set}";
    }
}