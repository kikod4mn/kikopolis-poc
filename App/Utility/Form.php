<?php

declare(strict_types=1);

namespace Kikopolis\App\Utility;

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
    public static function text(string $name, string $id, string $label, string $placeholder, string $class = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline', string $args = ''): string
    {
        $field = <<<HEREDOC
            <label for="{$id}" class="block text-gray-700 text-sm font-bold mb-2">{$label}</label>
            <input type="text" name="{$name}" id="{$id}" class="{$class}" {$args} placeholder="{$placeholder}">
HEREDOC;
        return $field;
    }

    public static function email(string $name, string $id, string $label, string $placeholder, string $class = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline', string $args = ''): string
    {
        $field = <<<HEREDOC
            <label for="{$id}" class="block text-gray-700 text-sm font-bold mb-2">{$label}</label>
            <input type="email" name="{$name}" id="{$id}" class="{$class}" {$args} placeholder="{$placeholder}">
HEREDOC;
        return $field;
    }

    public static function password(string $name, string $id, string $label, string $placeholder, string $class = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline', string $args = ''): string
    {
        $field = <<<HEREDOC
            <label for="{$id}" class="block text-gray-700 text-sm font-bold mb-2">{$label}</label>
            <input type="password" name="{$name}" id="{$id}" class="{$class}" {$args} placeholder="{$placeholder}">
HEREDOC;
        return $field;
    }

    public static function textarea(string $name, string $id, string $label, string $class = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline', string $args = ''): string
    {
        $field = <<<HEREDOC
            <label for="{$id}" class="block text-gray-700 text-sm font-bold mb-2">{$label}</label>
            <textarea type="password" name="{$name}" id="{$id}" class="{$class}" {$args}></textarea>
HEREDOC;
        return $field;
    }

    public static function submit(string $name = 'submit', string $value ='Submit')
    {
        $field = <<<HEREDOC
            <input type="submit" name="{$name}" value="{$value}">
HEREDOC;
        return $field;
    }
}