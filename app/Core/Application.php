<?php

namespace GreatMarketrealmCompanion\Core;

use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;
use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Resources\ResourceRegistry;

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
     * Register a service binding.
     */
    public function bind(
        string $abstract,
        mixed $concrete = null
    ): void {
        $this->container->bind(
            $abstract,
            $concrete
        );
    }
    
    /**
     * Register an existing service instance.
     */
    public function instance(
        string $abstract,
        mixed $instance
    ): void {
        $this->container->instance(
            $abstract,
            $instance
        );
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
     * Register a shared service in the container.
     */
    public function singleton(
        string $abstract,
        mixed $concrete = null
    ): void {
        $this->container->singleton(
            $abstract,
            $concrete
        );
    }
    
    /**
     * Register a service binding in the container.
     */
    public function bind(
        string $abstract,
        mixed $concrete = null
    ): void {
        $this->container->bind(
            $abstract,
            $concrete
        );
    }
    
    /**
     * Register an existing service instance.
     */
    public function instance(
        string $abstract,
        mixed $instance
    ): void {
        $this->container->instance(
            $abstract,
            $instance
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

    /**
     * Return the navigation service.
     */
    public function navigation(): Navigation
    {
        return $this->make(
            Navigation::class
        );
    }
    
    /**
     * Return the routing service.
     */
    public function route(): Router
    {
        return $this->make(
            Router::class
        );
    }

    /**
     * Return the Kingdom registry.
     */
    public function kingdoms(): KingdomRegistry
    {
        return $this->make(
            KingdomRegistry::class
        );
    }

    /**
     * Access the application Resource registry.
     */
    public function resources(): ResourceRegistry
    {
        return $this->make(
            ResourceRegistry::class
        );
    }

    /**
     * Retrieve the current HTTP request.
     */
    public function request(): Request
    {
        return $this->make(
            Request::class
        );
    }
    
    /**
     * Retrieve the HTTP response factory.
     */
    public function response(): ResponseFactory
    {
        return $this->make(
            ResponseFactory::class
        );
    }
    
    /**
     * Retrieve the application router.
     */
    public function router(): Router
    {
        return $this->make(
            Router::class
        );
    }
}
