<?php

namespace Kikopolis\Core;

use ReflectionClass;

defined('_KIKOPOLIS') or die('No direct script access!');

class Container
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Undocumented function
     *
     * @param [type] $abstract
     * @param [type] $concrete
     * @return void
     */
    public function set($abstract, $concrete = null)
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->instances[$abstract] = $concrete;
    }

    /**
     * Undocumented function
     *
     * @param [type] $abstract
     * @param array $parameters
     * @return void
     */
    public function get($abstract, $parameters = [])
    {
        // If not registered, then do so
        if (!isset($this->instances[$abstract])) {
            $this->set($abstract);
        }
        return $this->resolve($this->instances[$abstract], $parameters);
    }

    /**
     * Resolve a class
     *
     * @throws Exception
     * @param [type] $concrete
     * @param [type] $parameters
     * @return mixed|object
     */
    public function resolve($concrete, $parameters)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        // Check if the class is instantiable
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable!");
        }
        // Get class constructor
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            // Get new instance from the class
            return $reflector->newInstance();
        }
        // Get constructor parameters
        $parameters = $constructor->getParameters();
        $method = $reflector->getMethod();
        // $method->getParameters();
        var_dump($method);
        $dependencies = $this->getDependencies($parameters);
        // Get new instance with dependencies resolved
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Undocumented function
     *
     * @throws Exception
     * @param [type] $parameters
     * @return array
     */
    public function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            // Get the type hinted class
            $dependency = $parameter->getClass();
            if ($dependency === null) {
                // Check if default value for a parameter is available
                if ($parameter->isDefaultValueAvailable()) {
                    // Get default value of parameter
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Can not resolve class dependency {$parameter->name}");
                }
            } else {
                // Get dependency resolved
                $dependencies[] = $this->get($dependency->name);
            }
        }
        return $dependencies;
    }
}