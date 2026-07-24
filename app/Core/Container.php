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
     * Factory bindings.
     *
     * @var array<string, callable>
     */
    protected array $bindings = [];

    /**
     * Singleton factories.
     *
     * @var array<string, callable>
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
     * Used to detect circular dependencies.
     *
     * @var array<int, string>
     */
    protected array $resolving = [];

    /**
     * Register a factory binding.
     */
    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    /**
     * Register a singleton binding.
     */
    public function singleton(string $abstract, callable $factory): void
    {
        $this->singletons[$abstract] = $factory;
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
     * @throws ContainerException
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->singletons[$abstract])) {
            $this->instances[$abstract] = ($this->singletons[$abstract])(
                $this,
                $parameters
            );

            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            return ($this->bindings[$abstract])(
                $this,
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
