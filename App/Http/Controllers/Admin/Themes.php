<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Framework\Theme;
use Kikopolis\App\Framework\Aurora\View;
use Kikopolis\App\Framework\Controllers\Controller;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Themes
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Themes extends Controller
{
	protected $middleware = ['auth', 'admin'];

    public function indexAction()
    {
		return View::render('admin.theme.index', []);
    }

    public function createAction()
    {
		return View::render('admin.theme.create', []);
    }

    public function insertAction(Theme $theme)
    {
		if ($theme->save($_POST)) {
			redirect('theme/index');
			withMessage('New entry added!');
		} else {
			return returnTo();
		}
    }

    public function editAction()
    {
		return View::render('admin.theme.edit', []);
    }

    public function updateAction(Theme $theme)
    {
		if ($theme->update($_POST)) {
			redirect('theme/index');
			withMessage('Entry modified');
		} else {

			return returnTo();
		}
    }

    public function deleteAction(Theme $theme)
    {
		if ($theme->delete($this->params['id'])) {
			redirect('theme/index');
			withMessage('Entry deleted');
		} else {

			return returnTo();
		}
    }
}