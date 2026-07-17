<?php

namespace GreatMarketrealmCompanion\Core;

defined('ABSPATH') || exit;

/**
 * Marketrealm Companion
 *
 * Application
 *
 * The central application object responsible for bootstrapping
 * and coordinating the Marketrealm Companion Platform.
 *
 * @package MarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class Application
{
    /**
     * Platform version.
     */
    protected string $version;

    /**
     * Dependency container.
     */
    protected Container $container;

    /**
     * Framework kernel.
     */
    protected Kernel $kernel;

    /**
     * Constructor.
     */
    public function __construct(string $version)
    {
        $this->version = $version;
    
        $this->container = new Container();
    
        /*
         * Register the application instance.
         */
        $this->container->instance(
            self::class,
            $this
        );
    
        /*
         * Register the container instance.
         */
        $this->container->instance(
            Container::class,
            $this->container
        );
    
        $this->kernel = new Kernel($this);
    }

    /**
     * Boot the platform.
     */
    public function boot(): void
    {
        $this->kernel->boot();
    }

    /**
     * Return the framework kernel.
     */
    public function kernel(): Kernel
    {
        return $this->kernel;
    }

    /**
     * Return the dependency container.
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * Resolve a service.
     */
    public function make(
        string $abstract,
        array $parameters = []
    ): mixed {
        return $this->container->make(
            $abstract,
            $parameters
        );
    }

    /**
     * Determine whether a service exists.
     */
    public function has(string $abstract): bool
    {
        return $this->container->has($abstract);
    }

    /**
     * Return the platform version.
     */
    public function version(): string
    {
        return $this->version;
    }
}
