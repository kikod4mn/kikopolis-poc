<?php

namespace Kikopolis\Core\Aurora;

class View
{
    public static function render($file_name, $template_variables)
    {
        $template_file = '';
        $file_contents = '';

        $template = new Aurora($file_name);
        extract($template_variables, EXTR_SKIP);
        // Check to see if user defined functions are present.
        // If the functions array is not empty, the template is recompiled every time with user functions.
        // It is best to write a TODO: and say TODO: write custom extensions and test those.
        if (Aurora::$must_run_user_func === true) {
            $template_file_contents = '';
            try {
                $template_file_contents = $template->output(true);
            } catch (\Exception $e) {
            }
            $file_contents = file_get_contents($template_file_contents);
            $file_contents = Aurora::runUserFunc($file_contents);
            $template_file = $template->saveToCachedFile($file_contents);
        } else {
            // Set it to true for testing, always get a recompiled template.
            try {
                $template_file = $template->output();
            } catch (\Exception $e) {
            }
        }

        // Show the template page
        require_once $template_file;

        // Test to see how the template generation after template serving to the browser works out.
        // sleep(5);
        die;
        $test_var = file_get_contents($template->output(true));
        $cached_contents = file_get_contents($template_file);
        // @TODO: Due to the nature of php, this works on refresh if we refresh twice, meaning first time it still sends compiled if file exists and time is good
        // and then checks for differences, rendering the new page.
        if ($test_var !== $cached_contents) {
            $template->output(true);
        } elseif ($test_var === $cached_contents) {
            echo '<h2 style="color:red;background-color:black;text-align:center;">Contents match, no re-compile necessary</h2>';
        }
    }

    // @TODO: write custom functions into render
    public static function addFunction($name, $callback, $arguments = [])
    {
        AuroraFunctionHelper::addFunction($name, $callback, $arguments);
    }
}
