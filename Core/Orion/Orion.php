<?php

namespace Kikopolis\Core\Orion;

use App\Models\User;
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
     * @param int $limit
     * @return array
     */
    final public function get(array $columns = ['*'], int $limit = 5)
    {
        $return_obj = (object) [];
        $cols = implode(',', $columns);
        $class = explode('\\', get_called_class());
        $this->query('SELECT ' . $cols . ' FROM ' . end($class) . 's LIMIT ' . $limit);
        $raw = $this->resultSet();
        foreach ($raw as $single) {
            foreach ($single as $col => $value) {
                $model = new $this($single);
            }
            $name = random_bytes(8).random_int(1111, 9999).rand(0000, 9999);
            $return_obj->{$name} = $model->attributes;
        }
        return $return_obj;
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
