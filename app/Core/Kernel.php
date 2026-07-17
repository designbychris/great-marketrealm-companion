<?php

namespace GreatMarketrealmCompanion\Core;

defined('ABSPATH') || exit;

/**
 * Framework Kernel.
 *
 * Coordinates the platform boot process.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class Kernel
{
    /**
     * Application instance.
     */
    protected Application $app;

    /**
     * Core provider class names.
     *
     * @var array<int, class-string<ServiceProvider>>
     */
    protected array $providerClasses = [
        NavigationServiceProvider::class,
    ];

    /**
     * Loaded provider instances.
     *
     * @var ServiceProvider[]
     */
    protected array $providers = [];

    /**
     * Constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Boot the application.
     */
    public function boot(): void
    {
        $this->registerProviders();
    
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
    
        do_action(
            'gmrc_booted',
            $this->app
        );
    }

    /**
     * Register a service provider.
     */
    protected function register(
    ServiceProvider $provider
    ): void
    {
        $this->providers[] = $provider;
    
        $provider->register();
    }

    /**
     * Register all core service providers.
     */

    protected function registerProviders(): void
    {
            foreach ($this->providerClasses as $providerClass) {
        
                $this->register(
                    new $providerClass($this->app)
                );
    
                // $this->register(new NavigationServiceProvider($this->app));
                // $this->register(new ViewServiceProvider($this->app));
                // $this->register(new ModuleServiceProvider($this->app));
                // $this->register(new RouteServiceProvider($this->app));
        
            }
        }

    /**
     * Return registered providers.
     *
     * @return ServiceProvider[]
     */
    public function providers(): array
    {
        return $this->providers;
    }
}
