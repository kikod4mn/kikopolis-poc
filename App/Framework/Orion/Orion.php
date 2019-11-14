<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion;

use Kikopolis\App\Framework\Orion\OrionTraits\ExecutionTrait;
use Kikopolis\App\Framework\Orion\OrionTraits\PropertiesTrait;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Orion database ORM.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

abstract class Orion
{
    use PropertiesTrait, ExecutionTrait;


}
