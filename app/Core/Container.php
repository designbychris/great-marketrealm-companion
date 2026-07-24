<?php

namespace GreatMarketrealmCompanion\Core;

use GreatMarketrealmCompanion\Exceptions\ContainerException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

defined('ABSPATH') || exit;

/**
 * Marketrealm Companion
 *
 * Dependency Container
 *
 * A lightweight dependency injection container responsible for
 * registering and resolving services throughout the platform.
 *
 * @package MarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class Container
{
    /**
     * Service bindings.
     *
     * @var array<string, callable|string>
     */
    protected array $bindings = [];
    
    /**
     * Singleton bindings.
     *
     * @var array<string, callable|string>
     */
    protected array $singletons = [];

    /**
     * Instantiated singleton objects.
     *
     * @var array<string, object>
     */
    protected array $instances = [];

    /**
     * Classes currently being resolved.
     *
     * @var array<int, string>
     */
    protected array $resolving = [];

    /**
     * Register a service binding.
     *
     * When no concrete implementation is supplied, the abstract
     * class itself will be resolved through auto-wiring.
     */
    public function bind(
        string $abstract,
        callable|string|null $concrete = null
    ): void {
        $this->bindings[$abstract] = $concrete
            ?? $abstract;
    
        unset(
            $this->singletons[$abstract],
            $this->instances[$abstract]
        );
    }

    /**
     * Register a singleton binding.
     *
     * When no concrete implementation is supplied, the abstract
     * class itself will be resolved through auto-wiring.
     */
    public function singleton(
        string $abstract,
        callable|string|null $concrete = null
    ): void {
        $this->singletons[$abstract] = $concrete
            ?? $abstract;
    
        unset(
            $this->bindings[$abstract],
            $this->instances[$abstract]
        );
    }

    /**
     * Register an existing instance.
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve an object from the container.
     *
     * @param array<string, mixed> $parameters
     *
     * @throws ContainerException
     */
    public function make(
        string $abstract,
        array $parameters = []
    ): mixed {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

       if (isset($this->singletons[$abstract])) {
            $this->instances[$abstract] = $this->resolveBinding(
                $this->singletons[$abstract],
                $parameters
            );
        
            return $this->instances[$abstract];
        }
        
        if (isset($this->bindings[$abstract])) {
            return $this->resolveBinding(
                $this->bindings[$abstract],
                $parameters
            );
        }

        if (class_exists($abstract)) {
            return $this->build(
                $abstract,
                $parameters
            );
        }

        throw new ContainerException(
            sprintf(
                'Nothing has been registered for [%s].',
                $abstract
            )
        );
    }

    /**
     * Resolve a registered binding.
     *
     * @param callable|string        $concrete
     * @param array<string, mixed>   $parameters
     *
     * @throws ContainerException
     */
    protected function resolveBinding(
        callable|string $concrete,
        array $parameters = []
    ): mixed {
        if (is_callable($concrete)) {
            return $concrete(
                $this,
                $parameters
            );
        }
    
        return $this->build(
            $concrete,
            $parameters
        );
    }

    /**
     * Build a concrete class using constructor injection.
     *
     * @param class-string         $concrete
     * @param array<string, mixed> $parameters
     *
     * @throws ContainerException
     */
    protected function build(
        string $concrete,
        array $parameters = []
    ): object {
        if (in_array($concrete, $this->resolving, true)) {
            throw new ContainerException(
                sprintf(
                    'Circular dependency detected while resolving [%s].',
                    $concrete
                )
            );
        }

        $reflection = new ReflectionClass(
            $concrete
        );

        if (! $reflection->isInstantiable()) {
            throw new ContainerException(
                sprintf(
                    'Class [%s] is not instantiable.',
                    $concrete
                )
            );
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $this->resolving[] = $concrete;

        try {
            $dependencies = [];

            foreach ($constructor->getParameters() as $parameter) {
                $dependencies[] = $this->resolveDependency(
                    $parameter,
                    $parameters
                );
            }

            return $reflection->newInstanceArgs(
                $dependencies
            );
        } finally {
            array_pop($this->resolving);
        }
    }

    /**
     * Resolve a constructor dependency.
     *
     * @param array<string, mixed> $parameters
     *
     * @throws ContainerException
     */
    protected function resolveDependency(
        ReflectionParameter $parameter,
        array $parameters
    ): mixed {
        $name = $parameter->getName();

        if (array_key_exists($name, $parameters)) {
            return $parameters[$name];
        }

        $type = $parameter->getType();

        if (
            $type instanceof ReflectionNamedType
            && ! $type->isBuiltin()
        ) {
            return $this->make(
                $type->getName()
            );
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        throw new ContainerException(
            sprintf(
                'Unable to resolve parameter [$%s] while building [%s].',
                $name,
                $parameter->getDeclaringClass()?->getName()
                    ?? 'unknown class'
            )
        );
    }
    
    /**
     * Determine if an abstract has been registered.
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract])
            || isset($this->singletons[$abstract])
            || isset($this->instances[$abstract]);
    }

    /**
     * Remove a binding from the container.
     */
    public function forget(string $abstract): void
    {
        unset(
            $this->bindings[$abstract],
            $this->singletons[$abstract],
            $this->instances[$abstract]
        );
    }
}
