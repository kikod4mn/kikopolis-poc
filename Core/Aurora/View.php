<?php

namespace Kikopolis\Core\Aurora;

class View
{
    public static function render($file_name, $template_variables)
    {
        $template = new Aurora($file_name, $template_variables);
        extract($template_variables);
        // Set it to true for testing, always get a recompiled template.
        $template_test = $template->output(true);
        require_once $template_test;
        // echo $template->output();

        // Test to see how the template generation after template serving to the browser works out.
        die;
        $test_var = file_get_contents($template->output(true));
        $cached_contents = file_get_contents($template_test);
        // var_dump($test_var);
        // var_dump($cached_contents);
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
