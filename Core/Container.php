<?php

namespace Kikopolis\Core;

use ReflectionClass;
use ReflectionMethod;

defined('_KIKOPOLIS') or die('No direct script access!');

class Container
{
    /**
     * Array of class instances.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Set the class names with namespace to instances array.
     *
     * @param string $abstract The name of the class
     * @param string $concrete The name of the class
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
     * Get the class together with its dependencies.
     *
     * @param string $abstract
     * @param array $parameters
     * @return void
     */
    public function get($abstract, $method = null, $parameters = [])
    {
        // If not registered, then do so
        if (!isset($this->instances[$abstract])) {
            $this->set($abstract);
        }
        return $this->resolve($this->instances[$abstract], $parameters, $method);
    }

    /**
     * Resolve a class
     *
     * @throws Exception
     * @param string $concrete
     * @param array $parameters
     * @param string $method
     * @return mixed|object
     */
    public function resolve($concrete, $parameters, $method)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        // Check if the class is instantiable
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable!");
        }
        if ($method !== null) {
            $method_dependencies = new ReflectionMethod($concrete, $method);
            var_dump($method_dependencies);
            $method_dependencies_array = $method_dependencies->getParameters();
            var_dump($method_dependencies_array);
            $method_dependencies_array = $this->getDependencies($method_dependencies_array);
            var_dump($method_dependencies_array);
            // die;
        }
        // Get class constructor
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            // Get new instance from the class
            $constructor = $reflector->newInstance();
        } else {
            // Get constructor parameters
            $parameters = $constructor->getParameters();
            $dependencies = $this->getDependencies($parameters);
            $constructor = $reflector->newInstanceArgs($dependencies);
        }

        // Get new instance with dependencies resolved
        if ($method !== null) {
            $method_dependencies = $method_dependencies->invokeArgs($constructor, $method_dependencies_array);
            var_dump($method_dependencies);
            var_dump($constructor->$method($method_dependencies));
            die;
            return $constructor->$method($method_dependencies);
        } else {
            return $constructor;
        }
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