<?php

namespace Kikopolis\Core\Aurora;

class View
{
    public static function render($file_name, $template_variables)
    {
        $template = new Aurora($file_name, $template_variables);
        echo $template->output();
    }
}