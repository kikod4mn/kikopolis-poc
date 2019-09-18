<?php

namespace Kikopolis\Core\Aurora;

class View
{
    public static function render($file_name, $template_variables)
    {
        $template = new Aurora($file_name, $template_variables);
        extract($template_variables);
        require_once $template->output();
        // echo $template->output();
    }
}
