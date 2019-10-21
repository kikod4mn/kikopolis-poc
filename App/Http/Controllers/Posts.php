<?php

declare(strict_types=1);

namespace App\Http\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

use App\Models\Post;
use Kikopolis\App\Framework\Aurora\View;
use Kikopolis\App\Framework\Controllers\Controller;

class Posts extends Controller
{
    protected $middleware = ['auth'];

    public function indexAction()
    {
        View::render('posts.index', []);
    }

    public function showAction()
    {
        View::render('posts.show', []);
    }

    public function createAction()
    {
        View::render('posts.new', []);
    }

    public function saveAction(Post $post)
    {
        $post->save($_POST);
        View::render('posts.save-success', []);
    }

    public function editAction()
    {
        View::render('posts.edit', []);
    }

    public function update(Post $post)
    {
        $post->update($_POST);
        View::render('posts.update-success', []);
    }

    public function delete(Post $post)
    {
        $post->delete();
        View::render('posts.remove-success', []);
    }
}
