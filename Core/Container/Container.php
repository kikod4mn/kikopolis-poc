<?php

namespace Kikopolis\Core\Container;

use ReflectionClass;
use ReflectionMethod;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * IoC Dependency Injection Container class.
 * 
 * @author Kristo Leas <admin@kikopolis.com>
 * PHP Version 7.3.5
 */
class Container
{
    /**
     * Array of class instances.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Set the class name to instances array.
     *
     * @param string $abstract The name of the class.
     * @param string $concrete The name of the class with namespace.
     * @return void
     */
    public function set(string $abstract, string $concrete = ''): void
    {
        if ($concrete === '') {
            $concrete = $abstract;
        }
        $this->instances[$abstract] = $concrete;
    }

    /**
     * Get the class together with its dependencies.
     *
     * @param string $abstract
     * @param string $method
     * @param array $parameters
     * @return void
     */
    public function get(string $abstract, string $method = '', array $parameters = [])
    {
        // If not registered, then register the $abstract first.
        if (!isset($this->instances[$abstract])) {
            $this->set($abstract);
        }
        return $this->buildInstances($this->instances[$abstract], $parameters, $method);
    }

    /**
     * Make and resolve a namespaced class instance.
     *
     * @param       string      $concrete
     * @param       array       $parameters
     * @param       string      $method
     * @throws Exception
     * @return      mixed
     */
    private function buildInstances(string $concrete, array $parameters, string $method = '')
    {
        // If $concrete is a closure, return it immediately.
        if ($concrete instanceof Closure) {
            return $concrete($this, ...$parameters);
        }
        // Initialize variables.
        $reflector = null;
        $dependencies = [];
        $instances = [];
        // Get a new ReflectionClass for our $concrete.
        $reflector = new ReflectionClass($concrete);
        // If $reflector is not instantiable, meaning it is an Abstract or Interface class.
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable!");
        }
        // Get class constructor.
        $constructor = $reflector->getConstructor();
        // If no constructor, then no dependencies - simply return new class instance.
        if (is_null($constructor)) {
            return new $concrete;
        }
        // Get constructor params.
        $dependencies = $constructor->getParameters();
        // Resolve constructor params.
        $instances = $this->resolve($dependencies);
        // If a method is passed in, resolve the methods dependencies.
        // The method would most likely come from the Router class.
        if ($method !== '') {
            return $this->resolveMethod($concrete, $method);
        }
        // Instantiate the class with arguments.
        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Instantiates method with resolved dependencies.
     *
     * @param string $concrete
     * @param string $method
     * @return mixed
     */
    private function resolveMethod(string $concrete, string $method)
    {
        // Initialize variables
        $instance = null;
        $dependencies = [];
        $instances = [];
        // Get new ReflectionMethod instance for our $method.
        $instance = new ReflectionMethod($concrete, $method);
        // Get the $method dependencies and resolve them.
        $dependencies = $instance->getParameters();
        $instances = $this->resolve($dependencies);
        // Instantiate the method with its parameters resolved.
        return $instance->invokeArgs(new $concrete, $instances);
    }

    /**
     * Resolve the dependencies of a class or method.
     *
     * @param array $parameters
     * @throws Exception
     * @return array
     */
    private function resolve(array $parameters): array
    {
        // Initialize variables
        $dependency = null;
        $dependencies = [];
        // Loop through all the parameters and get their respective class
        foreach ($parameters as $parameter) {
            // Get the type hinted class
            $dependency = $parameter->getClass();
            if ($dependency === null) {
                // Check if default value for a dependency parameter is available
                if ($parameter->isDefaultValueAvailable()) {
                    // Get the default value of parameter
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Can not resolve class dependency {$parameter->name}");
                }
            } else {
                // Get the dependency class resolved with its own possible dependencies
                $dependencies[] = $this->get($dependency->name);
            }
        }
        // Return the resolved dependencies array
        return $dependencies;
    }
}
