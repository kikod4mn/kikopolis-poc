<?php

declare(strict_types=1);

namespace Kikopolis\Core\Aurora;

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
     * Render the template and require the file.
     * @param string $file_name
     * @param array $template_variables
     * @param bool $force_compile
     * @return void
     * @throws \Exception
     */
    public static function render(string $file_name, array $template_variables = [], bool $force_compile = false)
    {
        // Initialize a new instance of Aurora with the template name.
        static::$template = new Aurora($file_name);
        // Check if user has defined custom functions.
        // If defined, the template will be recompiled on every request.
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
        // Show the template page
        require_once static::$output_file;
    }

    /**
     * Get the template file contents with user functions parsed.
     * @return bool|string
     * @throws \Exception
     * @return string
     */
    private static function getTemplateWithUserFunc(): string
    {
        static::$template_file = static::$template->output();
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
