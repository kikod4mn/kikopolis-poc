<?php

declare(strict_types=1);

namespace Kikopolis\Core\Orion;

use PDO;
use Kikopolis\App\Config\Config;
use Kikopolis\Core\Orion\Orion;

/**
 * The base model with PDO connection
 */

class Model extends Orion
{
    /**
     * Model constructor.
     * @param array $attributes The attributes of a model Class. Will be mapped according to fillable array.
     */
    final public function __construct($attributes = [])
    {
        if (!isset($this->db)) {
            $this->db = $this->getDb();
        }
        if (method_exists(get_called_class(), '__constructor')) {
            $this->__constructor();
        }
        if ($attributes !== []) {
            $this->fill($attributes);
        }
    }
}
