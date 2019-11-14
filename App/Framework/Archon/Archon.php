<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Archon;

use Kikopolis\App\Utility\Regexp;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Archon
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Archon
{
	private $regex = '/@theme::(?<var>\w*)/';

	public function process(string $output): string
	{
		$matches = Regexp::findByRegex($this->regex, $output);
		foreach ($matches as $match) {
			$output = preg_replace('/' . $match[0] . '/', "<?php echo \Kikopolis\App\Framework\Archon\Archon::parse(\$theme->{$match['var']}); ?>" , $output);
		}

		return $output;
    }

	public static function parse($variable)
	{
		return $variable;
    }
}