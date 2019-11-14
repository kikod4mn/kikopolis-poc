<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Aurora;

use App\Models\Framework\Content;
use App\Models\Framework\Tag;
use App\Models\Framework\Theme;
use Kikopolis\App\Auth\Auth;
use Kikopolis\App\Config\Config;
use Kikopolis\App\Framework\Cardinal\Cardinal;
use Kikopolis\App\Framework\Corallize\Corallize;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * View class. This class is the public access for Aurora template engine and static template files.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class View
{
    /**
     * @var Aurora
     */
    private static $template;

    /**
     * @var string
     */
    private static $output_file = '';

    /**
     * @var string
     */
    private static $template_file = '';

    /**
     * @var string
     */
    private static $template_file_contents = '';

	/**
	 * @var array
	 */
    private static $content = [];

    /**
	 * @var array
	 */
	private static $theme = [];

	/**
	 * @var array
	 */
	private static $tags = [];

	/**
     * Render the template and require the file.
     * @param string $file_name
     * @param array $template_variables
     * @param bool $force_compile
     * @return mixed
     * @throws \Exception
     */
    public static function render(string $file_name, array $template_variables = [], bool $force_compile = true)
    {
        // Initialize a new instance of Aurora with the template name.
        static::$template = new Aurora($file_name);
        // Check if user has defined custom functions.
        // If defined, the template will be recompiled on every request.
        // TODO: Extract user functions to separate class static function to enable running them on a compiled template.
        if (Aurora::$must_run_user_func === true) {
            static::$output_file = static::getTemplateWithUserFunc();
        } else {
            if (static::$template->getCacheExists() === true && $force_compile === false) {
                // Cache exists, no force compile.
                static::$output_file = static::$template->getCachedFile();
            } else if ($force_compile === true) {
                // Force compile set, dont even care about cache.
                static::$output_file = static::$template->output();
            } else {
                // No cache, no force compile. Default to recompile.
                static::$output_file = static::$template->output();
            }
        }
        // Extract template variables
        extract($template_variables, EXTR_SKIP);
        $flash_messages = getFlashMessages();
        if (Config::ENABLE_CARDINAL === true) {
			self::$content = self::getContent($file_name);
            extract(self::$content);
        }
        if (Config::ENABLE_CORALLIZE === true) {
        	Corallize::setTags(self::getTags());
		}
        if (Config::ENABLE_ARCHON === true) {
			self::$theme = self::getTheme();
			extract(self::$theme);
		}

        return require_once static::$output_file;
    }

	private static function getContent($file_name)
	{
		$return = [];
		$content = new Content();
		$data = $content->find('page_route', $file_name);
		if (is_string($data->content)) {
			$return = (array) json_decode($data->content);
		}

		return $return;
    }

	private static function getTags()
	{
		$tags = new Tag();
		return $tags->all();
    }

	private static function getTheme()
	{
		$return = [];
		$theme = new Theme();
		$data = $theme->find('name', Config::THEME);
		if (is_string($data->variables)) {
			$return = (array) json_decode($data->variables);
		}

		return $return;
    }

    /**
     * Get the template file contents with user functions parsed.
     * @return bool|string
     * @throws \Exception
     * @return string
     */
    private static function getTemplateWithUserFunc(): string
    {
        static::$template_file = static::$template->output(true);
        static::$template_file_contents = file_get_contents(static::$template_file);
        static::$template_file_contents = Aurora::runUserFunc(static::$template_file_contents);

        return static::$template->saveToCachedFile(static::$template_file_contents);
    }

    /**
     * Add a custom function to the user functions array.
     * @param $name
     * @param $callback
     * @param array $arguments
     * @return void
     */
    public static function addFunction(string $name, \Closure $callback, array $arguments = []): void
    {
        AuroraFunctionHelper::addFunction($name, $callback, $arguments);
    }
}
