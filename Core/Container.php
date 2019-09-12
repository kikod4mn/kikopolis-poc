<?php

namespace Kikopolis\Core;

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
     * Set the class names with namespace to instances array.
     *
     * @param string $abstract The name of the class
     * @param string $concrete The name of the class
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
    protected function resolve(string $concrete, array $parameters, string $method)
    {
        // Initialize variables
        $reflector = null;
        $constructor_params = [];
        $method_instance = null;
        $method_dependencies = [];
        // Check if the $concrete passed in is an instance of Closure and if it is
        // we can immediately return it
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        // Check if the class is instantiable.
        // If it isn't, we are most likely dealing with an invalid argument such as an Interface
        // and in that case we will want to throw an error
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable!");
        }
        // If there is a method called with the class and not just the base class itself
        // then we will resolve the method and its dependencies
        if ($method) {
            $method_instance = new ReflectionMethod($concrete, $method);
            $method_dependencies = $method_instance->getParameters();
            $method_dependencies = $this->getDependencies($method_dependencies);
        }
        // Get class constructor
        $constructor = $reflector->getConstructor();
        //If constructor is null meaning there is no constructor we will then store the class instance
        // and return it below to the calling function
        if (is_null($constructor)) {
            // Get new instance of our reflection class
            $constructor = $reflector->newInstance();
        } else {
            // If there is a class constructor present, we check for its dependencies.
            // and if there are dependencies required, we will then resolve them and 
            // save the constructor with dependencies resolved for return below.
            $constructor_params = $constructor->getParameters();
            if ($constructor_params !== []) {
                $dependencies = $this->getDependencies($constructor_params);
                $constructor = $reflector->newInstanceArgs($dependencies);
            } else {
                // If constructor has no dependencies specified, just save a new instance of if.
                $constructor = $reflector->newInstance();
            }
        }
        // Get new instance with method dependencies resolved
        if ($method_dependencies !== []) {
            $method_dependencies = $method_instance->invokeArgs($constructor, $method_dependencies);
        } else {
            // If we have a method resolved then instantiate the method for display in the browser
            // If not, simply return the constructor to the calling function
            if ($method !== '') {
                // If there is a method defined then instantiate the class with the method for display
                return $constructor->$method();
            } else {
                // Return the resolved constructor
                return $constructor;
            }
        }
    }

    /**
     * Resolve the dependencies of a class constructor or method
     *
     * @throws Exception
     * @param array $parameters
     * @return array
     */
    protected function getDependencies(array $parameters): array
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