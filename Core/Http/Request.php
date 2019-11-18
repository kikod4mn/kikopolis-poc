<?php


namespace Kikopolis\Core\Http;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Request
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Request
{
    public $query = [];
    public $request = [];
    public $attributes = [];
    public $cookies = [];
    public $files = [];
    public $server = [];
    public $content = null;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    protected function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->query = $query;
        $this->request = $request;
        $this->attributes = $attributes;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->content = $content;
    }

    public static function createFromGlobals()
    {
        $request = self::createRequest($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
        return $request;
    }

    private static function createRequest(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        return new static($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function query()
    {
        return $this->newQuery();
    }

    private function newQuery()
    {
        return $_POST;
    }
}