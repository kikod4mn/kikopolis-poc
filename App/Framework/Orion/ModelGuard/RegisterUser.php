<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion\ModelGuard;

use App\Models\User;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * RegisterUserTrait
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class RegisterUser
{
    public static function createNew(array $data)
    {
        $user = new RegisterUser();
        return $user->saveUser($data);
    }

    protected function saveUser(array $data)
    {
        $user = new User();
        return $user->insert($data);
    }
}