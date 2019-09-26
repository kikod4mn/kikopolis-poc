<?php

namespace Kikopolis\Core\Orion;

use Kikopolis\App\Helpers\Arr;
use Kikopolis\Core\Orion\OrionTraits\ManagePropertiesTrait;
use Kikopolis\Core\Orion\OrionTraits\ManageQueryTempTrait;

defined('_KIKOPOLIS') or die('No direct script access!');

abstract class Orion
{
    use ManagePropertiesTrait, ManageQueryTempTrait;

    /**
     * Returns all results found in the specified columns.
     *
     * @param array $columns
     * @return void
     */
    final public function get(array $columns = ['*'], int $limit = 5)
    {
        $cols = implode(',', $columns);
        $class = explode('\\', get_called_class());
        $this->query('SELECT ' . $cols . ' FROM ' . end($class) . 's LIMIT ' . $limit);
        // return $this->resultSet();
        $raw = $this->resultSet();
        foreach ($raw as $single) {
            $new_arr[] = array_diff_key((array) $single, array_flip((array) $this->hidden));
        }
        return $new_arr;
    }

    final public function find()
    {
        $class = explode('\\', get_called_class());
        $this->query('SELECT * FROM ' . end($class) . 's');
        // return $this->resultSet();
        $arr = $this->result();
        $arr = array_diff_key((array) $arr, array_flip((array) $this->hidden));
        return $arr;
    }
}
