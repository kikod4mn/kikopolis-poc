<?php

namespace Kikopolis\Core\Container;

use App\Models\Post;
use Kikopolis\App\Helpers\Str;
use ReflectionClass;
use ReflectionMethod;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * IoC Container class.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Container
{
    /**
     * Array of class instances.
     * @var array
     */
    protected $instances = [];

    /**
     * @var \ReflectionClass
     */
    protected $reflector;

    /**
     * @var \ReflectionClass __construct
     */
    protected $construct;

    /**
     * An array of static bindings for the container to search.
     * @var array
     */
    private $bindings = [
        'config' => Kikopolis\App\Config\Config::class
    ];

    public function __construct()
    {
//        $this->instances = new \SplObjectStorage();
    }

    /**
     * Set the class name to instances array.
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
     * @param string $abstract
     * @param string $method
     * @param array $parameters
     * @throws \ReflectionException
     * @return array
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
     * @param $concrete
     * @param array $parameters
     * @param string $method
     * @throws \ReflectionException
     * @throws \Exception
     * @return      mixed
     */
    private function buildInstances($concrete, array $parameters, string $method = '')
    {
        // If $concrete is a closure, return it immediately.
        if ($concrete instanceof \Closure) {

            return $concrete($this, ...$parameters);
        }
        $dependencies = [];
        $instances = [];
        // Get a new ReflectionClass for our $concrete.
        $this->reflector = new ReflectionClass($concrete);
        // If reflector is not instantiable, meaning it is an Abstract or an Interface.
        if (!$this->reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable!");
        }
        // Get class constructor.
        $this->construct = $this->reflector->getConstructor();
        // If no constructor, then no dependencies - simply return new class instance.
        if (is_null($this->construct)) {
            // If a method is passed in, resolve and instantiate the method.
            if ($method !== '') {

                return $this->resolveMethod($concrete, $method);
            }

            return new $concrete;
        }
        // Get constructor params.
        $dependencies = $this->construct->getParameters();
        // Resolve constructor params.
        $instances = $this->resolve($dependencies);
        // If a method is passed in, resolve the methods dependencies and instantiate a new instance.
        // The method would most likely come from the Router class.
        if ($method !== '') {

            return $this->resolveMethod($concrete, $method);
        }

        // Instantiate the class with arguments.
        return $this->reflector->newInstanceArgs($instances);
    }

    /**
     * Instantiates method with resolved dependencies.
     *
     * @param string $concrete
     * @param string $method
     * @throws \ReflectionException
     * @throws \Exception
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
        if ($dependencies) {
            $instances = $this->resolve($dependencies);
        }

        // Instantiate the method with its parameters resolved.
        return $instance->invokeArgs(new $concrete, $instances);
    }

    /**
     * Resolve the dependencies of a class or method.
     *
     * @param array $parameters
     * @throws \Exception
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
