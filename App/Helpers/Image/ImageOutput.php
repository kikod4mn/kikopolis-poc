<?php

declare(strict_types=1);

namespace Kikopolis\App\Helpers\Image;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * ImageOutput
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class ImageOutput
{
	public static function thumbnail(array $image)
	{
		return $image['200'];
    }
}