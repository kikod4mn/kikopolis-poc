<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Framework\Tag;
use Kikopolis\App\Framework\Aurora\View;
use Kikopolis\App\Framework\Controllers\Controller;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Tags
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Tags extends Controller
{
	protected $middleware = ['auth', 'admin'];

	public function indexAction()
	{
		return View::render('admin.tag.index', []);
	}

	public function createAction()
	{
		return View::render('admin.tag.create', []);
	}

	public function insertAction(Tag $tag)
	{
		if ($tag->save($_POST)) {
			redirect('tag/index');
			withMessage('New entry added!');
		} else {
			return returnTo();
		}
	}

	public function editAction()
	{
		return View::render('admin.tag.edit', []);
	}

	public function updateAction(Tag $tag)
	{
		if ($tag->update($_POST)) {
			redirect('tag/index');
			withMessage('Entry modified');
		} else {

			return returnTo();
		}
	}

	public function deleteAction(Tag $tag)
	{
		if ($tag->delete($this->params['id'])) {
			redirect('tag/index');
			withMessage('Entry deleted');
		} else {

			return returnTo();
		}
	}
}