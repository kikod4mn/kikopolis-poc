<?php

declare(strict_types=1);

namespace App\Models\Migrations;

use Kikopolis\App\Framework\Orion\Migrator;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * ImagesMigration
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class ImagesMigration
{
    public function migrate()
    {
        $migrate = new Migrator();
        $migrate->table('images');
        $migrate->id();
        $migrate->field('title')->varchar()->finish();
        $migrate->field('slug')->varchar()->unique()->finish();
        $migrate->field('category')->varchar()->finish();
        $migrate->field('desc')->text()->finish();
        $migrate->field('size_s')->varchar()->null()->finish();
        $migrate->field('size_m')->varchar()->null()->finish();
        $migrate->field('size_l')->varchar()->null()->finish();
        $migrate->field('size_xl')->varchar()->null()->finish();
        $migrate->field('size_original')->varchar()->finish();
        $migrate->field('enable_gallery')->bool()->finish();
        $migrate->field('for_post')->bigInt()->null()->finish();
        $migrate->timestamps();
        $query = $migrate->create();
    }
}