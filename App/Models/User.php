<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Controllers\Authorization\Register;
use Kikopolis\App\Framework\Aurora\View;
use Kikopolis\Core\Model;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * User testing model
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class User extends Model
{
    /**
     * Put mass fillable model properties in this array.
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password_hash',
        'password_reset_hash',
        'password_reset_expires_at',
        'activation_hash',
        'is_active',
        'is_disabled',
        'account_level',
        'remember_token_hash',
        'remember_token_expires_at',
        'updated_at',
        'created_at'
    ];

    protected $visible = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at'
    ];

    /**
     * Put model properties that should remain hidden in this array.
     * @var array
     */
    protected $hidden = [
        'id',
        'name',
        'email',
        'password_hash',
        'password_reset_hash',
        'password_reset_expires_at',
        'activation_hash',
        'is_active',
        'is_disabled',
        'account_level',
        'remember_token_hash',
        'remember_token_expires_at',
        'updated_at',
        'created_at'
    ];

    /**
     * Save the model to the database.
     * @param array $data
     * @return bool|int Returns false on failure or last inserted id on success.
     * @throws \Exception
     */
    public function save(array $data)
    {
        return $this->insert($data);
    }

    /**
     * Update the model in the database.
     * Id must be in the $data array to find the db entry to modify.
     * @param $data
     * @return bool|int Returns false on failure or last modified id on success
     * @throws \Exception
     */
    public function update($data)
    {
        return $this->modify($data);
    }

    /**
     * Delete a model in the database.
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id)
    {
        return $this->destroy($id);
    }
}
