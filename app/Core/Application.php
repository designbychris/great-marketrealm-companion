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

        $this->registerBaseServices();

        $this->kernel = new Kernel($this);

        $this->container->instance(
            Kernel::class,
            $this->kernel
        );
    }

    /**
     * Register the core framework services.
     */
    protected function registerBaseServices(): void
    {
        $this->container->instance(
            self::class,
            $this
        );

        $this->container->instance(
            Container::class,
            $this->container
        );
    }

    /**
     * Boot the platform.
     */
    public function boot(): void
    {
        $this->kernel->boot();
    }

    /**
     * Get the framework kernel.
     */
    public function kernel(): Kernel
    {
        return $this->kernel;
    }

    /**
     * Get the dependency container.
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * Resolve a service from the container.
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
     * Determine if a service has been registered.
     */
    public function has(string $abstract): bool
    {
        return $this->container->has($abstract);
    }

    /**
     * Get the platform version.
     */
    public function version(): string
    {
        return $this->version;
    }
}
