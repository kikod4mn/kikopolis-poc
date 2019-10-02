<?php

declare(strict_types=1);

namespace Kikopolis\App\Config;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Configuration settings.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class ConfigExample
{
    /**
     * Represent the config option.
     * @var string|bool|int
     */
    protected $config;

    /**
     * Getter for the config option
     * @param string $option
     * @return string|bool|int
     */
    public function getConfig(string $option)
    {
        return $this->config[$option];
    }

    /**
     * @var boolean
     */
    const ENVIRONMENT = 'development';

    /**
     * Kikopolis application version number
     * @var string
     */
    const APPVERSION = '0.0.0.1000';

    /**
     * @var string
     */
    const DBHOST = '';

    /**
     * Database name
     * @var string
     */
    const DBNAME = '';

    /**
     * @var string
     */
    const DBUSER = '';

    /**
     * @var string
     */
    const DBPASS = '';

    /**
     * @var string
     */
    const SITENAME = 'Kikopolis MVC';

    /**
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret string
     * @var string
     */
    const STRING = '';

    /**
     * @var string
     */
    const ADMIN_EMAIL = '';

    /**
     * @var string
     */
    protected static $urlroot = '';

    /**
     * @var string
     */
    protected static $approot = '';

    /**
     * Return the app root.
     * @return string
     */
    public static function getAppRoot()
    {
        if (isset(static::$approot) && static::$approot !== '') {
            return static::$approot;
        }
        return rtrim(dirname(dirname(__DIR__)), '/');
    }

    /**
     * Return the url root.
     * @return string
     */
    public static function getUrlRoot()
    {
        if (isset(static::$urlroot) && static::$approot !== '') {
            return static::$urlroot;
        }
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        return $protocol . htmlspecialchars($_SERVER['HTTP_HOST']) . str_replace('/index.php', '', htmlspecialchars($_SERVER['SCRIPT_NAME']));
    }

    /**
     * Return the root dir for Views.
     * @return string
     */
    public static function getViewRoot()
    {
        return self::getAppRoot() . '/App/Views/';
    }

    /**
     * Return the root dir for assets.
     * @return string
     */
    public static function getAssetRoot()
    {
        return self::getUrlRoot();
    }

    /**
     * Return the root for cached files.
     * @return string
     */
    public static function getViewCacheRoot()
    {
        return self::getAppRoot() . '/App/Views/cache/';
    }

    /**
     * Return the root for application Controllers.
     * @return string
     */
    public static function getControllerRoot()
    {
        return self::getAppRoot() . '/App/Http/Controllers/';
    }
}
