<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion;

use Kikopolis\App\Helpers\Str;
use PDO;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * QueryBuilder
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class QueryBuilder
{
    /**
     * @var string
     */
    private $query = '';

    /**
     * Query type, can be SELECT, INSERT, DELETE, UPDATE.
     * @var string
     */
    private $query_type = 'SELECT';

    private $columns = '';

    private $first_result = null;

    private $max_results = null;

    private $parameters = [];

    private $parts = [
        'distinct' => false,
        'select' => [],
        'from' => [],
        'join' => [],
        'set' => [],
        'where' => null,
        'group_by' => [],
        'having' => null,
        'order_by' => [],
        'raw' => []
    ];

    public function __construct()
    {
        //
    }

    public function printQuery()
    {
        return $this->query;
    }

    public function create()
    {
//        $this->query = $this->create()->parameters()->firstResult()->lastResult()->maxResults();
        switch ($this->query_type) {
            case $this->query_type === 'SELECT':
                return $this->getSelectQuery();
            case $this->query_type === 'INSERT':
                return $this->getInsertQuery();
            case $this->query_type === 'UPDATE':
                return $this->getUpdateQuery();
            case $this->query_type === 'DELETE':
                return $this->getDeleteQuery();
        }
        throw new \Exception(sprintf("Cannot determine query type [%s]. QueryBuilder failed.", $this->query_type));
    }

    private function getSelectQuery()
    {
        foreach ($this->parts['select'] as $select) {
            $this->query .= ' ' . $select;
        }
        foreach ($this->parts['from'] as $from) {
            $alias_conflict = $this->aliasConflict();
            $this->query .= " FROM {$from['table']} AS {$from['alias']}";
        }
        foreach ($this->parts['where'] as $where) {
            $this->query .= " WHERE {$where['column']} = {$where['key']}";
        }
        if ($this->parts['raw'] !== []) {
            foreach ($this->parts['raw'] as $raw) {
                $this->query .= " {$raw}";
            }
        }
        unset($this->parts);

        return $this->query;
    }

    private function getInsertQuery()
    {
        foreach ($this->parameters as $parameter) {
            $this->query .= "{$parameter['key']} = :{$parameter['key']}, ";
        }
        $this->query = trim($this->query, "\,\ ");
        unset($this->parts);

        return $this->query;
    }

    private function getUpdateQuery()
    {
        foreach ($this->parameters as $parameter) {
            $this->query .= "{$parameter['key']} = :{$parameter['key']}, ";
        }
        $this->query = trim($this->query, "\,\ ");
        $this->buildWhere();
        unset($this->parts);

        return $this->query;
    }

    private function getDeleteQuery()
    {
        $this->buildWhere();
        unset($this->parts);

        return $this->query;
    }

    private function buildWhere()
    {
        foreach ($this->parts['where'] as $where) {
            $this->query .= " WHERE {$where['column']} = {$where['key']}";
        }
    }

    private function maxResults()
    {
        return $this;
    }

    public function select($columns = ['*'])
    {
        $this->query_type = 'SELECT';
        $this->query = 'SELECT';
        if ($columns !== ['*']) {
            $this->columns = $this->getSelectColumns($columns);
        } else {
            $this->columns = '*';
        }
        $this->parts['select'][] = $this->columns;

        return $this;
    }

    public function addSelect($cols = ['*'])
    {
        return $this;
    }

    private function getSelectColumns(array $columns)
    {
        $columns = implode(',', $columns);
        return $columns;
    }

    public function insert($table)
    {
        $this->query_type = 'INSERT';
        $this->query = "INSERT INTO {$table} SET ";
        return $this;
    }

    public function update($table)
    {
        $this->query_type = 'UPDATE';
        $this->query = "UPDATE {$table} SET ";
        return $this;
    }

    public function delete($table)
    {
        $this->query_type = 'DELETE';
        $this->query = "DELETE FROM {$table}";
        return $this;
    }

    protected function setParameters(array $parameters)
    {
        if ($parameters !== []) {
            foreach ($parameters as $key => $value) {
                if ($key !== 'id') {
                    $this->setParameter($key, $value);
                }
            }
        }
//        var_dump($parameters);
//        var_dump($this->parameters);
        return $this;
    }

    /**
     * Assign multiple parameters at once.
     * @param array $parameters
     * @return $this
     */
    public function parameters(array $parameters = [])
    {
        $this->setParameters($parameters);
        return $this;
    }

    /**
     * Assign a single parameter to the array.
     * @param $key
     * @param $value
     * @param null $type
     */
    protected function setParameter($key, $value, $type = null)
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

        $this->parameters[] = [
            'key' => $key,
            'value' => $value,
            'type' => $type
        ];
    }

    public function from($table, $alias = null)
    {
        if ($alias === null) {
            $alias = strtolower(substr($table, 0, 1));
        }
        $this->parts['from'][] = [
            'table' => $table,
            'alias' => $alias
        ];

        return $this;
    }

    public function join($table, $alias, $conditionType = null, $condition = null)
    {
        return $this;
    }

    public function leftJoin($table, $alias, $conditionType = null, $condition = null)
    {
        return $this;
    }

    public function innerJoin($table, $alias, $conditionType = null, $condition = null)
    {
        return $this;
    }

    public function where($key, $value = null)
    {
        $this->parts['where'][] = [
            'column' => $key,
            'key' => ":{$key}",
            'value' => $value
        ];

        return $this;
    }

    public function andWhere($col)
    {
        return $this;
    }

    public function orWhere($col)
    {
        return $this;
    }

    public function groupBy($col)
    {
        return $this;
    }

    public function addGroupBy($col)
    {
        return $this;
    }

    public function having($col)
    {
        return $this;
    }

    public function andHaving($col)
    {
        return $this;
    }

    public function orHaving($col)
    {
        return $this;
    }

    public function orderBy($col, $order = null)
    {
        return $this;
    }

    public function addOrderBy($col, $order = null)
    {
        return $this;
    }

    public function limit($limit)
    {
        return $this;
    }

    public function firstResult($first = 1)
    {
        return $this;
    }

    public function lastResult($last = 0)
    {
        return $this;
    }

    public function raw(string $sql)
    {
        $this->parts['raw'][] = $sql;
        return $this;
    }

    private function aliasConflict()
    {
    }


}