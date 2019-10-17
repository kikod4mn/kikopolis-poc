<?php

declare(strict_types=1);

namespace App\Http\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

use App\Models\Post;
use Kikopolis\App\Framework\Controllers\Controller;

class Posts extends Controller
{
    protected $middleware = ['auth'];

    public function indexAction()
    {
        // TODO: Implement method
    }

    public function showAction()
    {
        echo "<h1>The id of the post is {$this->params['id']}</h1>";
        echo "<h1>The slug of the post is {$this->params['slug']}</h1>";
        echo "Well done on reaching here, young padawan!!";
    }

    public function create()
    {
        // TODO: Implement method
    }

    public function save()
    {
        // TODO: Implement method
    }

    public function edit()
    {
        // TODO: Implement method
    }

    public function update()
    {
        // TODO: Implement method
    }

    public function delete()
    {
        // TODO: Implement method
    }
}
