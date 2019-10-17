<?php

declare(strict_types=1);

namespace Kikopolis\Core\Kernel;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Kernel
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Kernel
{
    public function __construct()
    {
        $this->autoload();
        $this->session();
        $this->cookie();
        $this->settings();
    }

    protected function settings()
    {
        /**
         * PHP Settings Development
         */
        ini_set('error_reporting', 'E_ALL');
        set_error_handler('Kikopolis\Core\Error::errorHandler');
        set_exception_handler('Kikopolis\Core\Error::exceptionHandler');
        ini_set("xdebug.var_display_max_children", '-1');
        ini_set("xdebug.var_display_max_data", '-1');
        ini_set("xdebug.var_display_max_depth", '-1');

        /**
         * PHP Settings Production
         */
        // ini_set('error_reporting', E_ALL ^ E_DEPRECATED);
        // set_error_handler('Kikopolis\Core\Error::errorHandler');
        // set_exception_handler('Kikopolis\Core\Error::exceptionHandler');
    }

    protected function cookie()
    {
        //
    }

    protected function session()
    {
        session_start();
    }

    protected function autoload()
    {
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
    }
}