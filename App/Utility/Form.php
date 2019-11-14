<?php

declare(strict_types=1);

namespace Kikopolis\App\Utility;

use Kikopolis\App\Helpers\Str;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Form
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Form
{
    public static function csrf()
    {
        $token = new Token();
        $csrf = $token->getCsrfToken();
        return <<<HEREDOC
            <input type="hidden" name="csrf_token" value="{$csrf}">
HEREDOC;
    }

	public static function formOpen()
	{
		
    }

	public static function formClose()
	{
		
    }

	public static function method($method)
	{
		return "<input type=\"hidden\" name=\"method\" value=\"{$method}\">";
    }

    public static function id($value)
    {
        return "<input type=\"hidden\" name=\"id\" value=\"{{ {$value} }}\">";
    }

    public static function hidden($name, $value)
    {
        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\">";
    }

    public static function text(string $name, string $id = 'null', string $label = 'null', string $value = 'null', string $placeholder = 'null', string $class = 'null', string $args = 'null'): string
    {
//		if (strlen($value) > 100) {
//			return self::textarea($name, $id, $label);
//		}
    	$label_tag = null;
    	$id_part = null;
    	if ($label !== 'null' && $id !== 'null') {
    		$label_tag = "<label for=\"{{ {$id} }}\"><?php echo \Kikopolis\App\Helpers\Str::toText(\${$label}); ?></label>";
    		$id_part = "id=\"{{ {$id} }}\" ";
		}

    	$group_start = "<div class=\"md-form\">";
    	$group_end = "</div>";
    	$field = "{$group_start}";
    	$field .= "<input type=\"text\" name=\"{{ {$name} }}\"";
		if ($label_tag !== null && $id_part !== null) {
			$field .= "{$id_part}";
		}
		if ($placeholder !== 'null') {
			$field .= " placeholder=\"{{ {$placeholder} }}\"";
		}
		if ($value !== 'null') {
			$field .= " value=\"{{ {$value} }}\"";
		}
		if ($args !== 'null') {
			$field .= "{$args}";
		}
		if ($class === 'null') {
			$class = "form-control my-3 py-2 px-3 text-dark";
		}
		$field .= " class=\"{$class}\"";
    	$field .= ">";
		if ($label_tag !== null && $id_part !== null) {
			$field .= $label_tag;
		}
		$field .= "{$group_end}";
        return $field;
    }

    public static function email(string $name, string $id, string $label, string $value = '', string $placeholder = '', string $class = 'form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline', string $args = ''): string
    {
        $field = <<<HEREDOC
            <div class="form-group">
                <label for="{$id}" class="block text-gray-700 text-sm font-bold mb-2">{$label}</label>
                <input type="email" name="{$name}" id="{$id}" class="{$class}" {$args} placeholder="{$placeholder}" value="{$value}">
            </div>
HEREDOC;
        return $field;
    }

    public static function password(string $name, string $id, string $label, string $placeholder, string $class = 'form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline', string $args = ''): string
    {
        $field = <<<HEREDOC
            <div class="form-group">
                <label for="{$id}" class="block text-gray-700 text-sm font-bold mb-2">{$label}</label>
                <input type="password" name="{$name}" id="{$id}" class="{$class}" {$args} placeholder="{$placeholder}">
            </div>
HEREDOC;
        return $field;
    }

    public static function textarea(string $name, string $id, string $label, string $value = '', string $class = 'form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline', string $args = ''): string
    {
        $field = <<<HEREDOC
            <div class="form-group">
                <label for="{$id}" class="block text-gray-700 text-sm font-bold mb-2">{$label}</label>
                <textarea type="password" name="{$name}" id="{$id}" class="{$class}" {$args}>{$value}</textarea>
            </div>
HEREDOC;
        return $field;
    }

    public static function submit(string $name = 'submit', string $value ='Submit')
    {
        $field = <<<HEREDOC
            <div class="form-group">
                <input type="submit" name="{$name}" value="{$value}" class="btn btn-lg btn-primary">
            </div>
HEREDOC;
        return $field;
    }
}