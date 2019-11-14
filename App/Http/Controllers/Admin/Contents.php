<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Framework\Content;
use Kikopolis\App\Framework\Aurora\View;
use Kikopolis\App\Framework\Controllers\Controller;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * CMS
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Contents extends Controller
{
	protected $middleware = ['auth', 'admin'];

    public function indexAction(Content $content)
    {
        $pages = $content->all();

        return View::render('admin.content.index', ['pages' => $pages]);
    }

    public function createAction()
    {
        return View::render('admin.content.create');
    }

    public function editAction(Content $content)
    {
        $page = $content->find($this->params['id']);

        return View::render('admin.content.edit', ['page' => $page]);
    }

    public function insertAction(Content $content)
    {
        if ($content->save($_POST)) {
            redirect('content/index');
            withMessage('New entry added!');
        } else {
            return returnTo();
        }
    }

    public function updateAction(Content $content)
    {
        if ($content->update($_POST)) {
            redirect('content/index');
            withMessage('Entry modified');
        } else {

        	return returnTo();
		}
    }

    public function deleteAction(Content $content)
    {
		if ($content->delete($this->params['id'])) {
			redirect('content/index');
			withMessage('Entry deleted');
		} else {

			return returnTo();
		}
    }
}