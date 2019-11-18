<?php

declare(strict_types=1);

namespace App\Models;

use Kikopolis\Core\Model;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Contact
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Contact extends Model
{
    /**
     * Put mass fillable model properties in this array.
     * @var array
     */
    protected $fillable = ['subject', 'message'];
}