<?php

namespace Kikopolis\Core\Orion;

use Kikopolis\App\Helpers\Arr;
use Kikopolis\Core\Orion\OrionTraits\ManagePropertiesTrait;

defined('_KIKOPOLIS') or die('No direct script access!');

abstract class Orion
{
    use ManagePropertiesTrait;

    final public function get($columns = ['*'])
    {
        $this->query('SELECT * FROM users');
        $arr = $this->resultSet();
        foreach ($arr as $user) {
            $user = array_diff_key((array) $user, array_flip((array) $this->hidden));
            $new_arr[] = $user;
        }
        var_dump($new_arr);
        return $new_arr;
    }
}
