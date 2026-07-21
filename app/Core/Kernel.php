<?php

namespace GreatMarketrealmCompanion\Core;

use GreatMarketrealmCompanion\Providers\NavigationServiceProvider;
use GreatMarketrealmCompanion\Providers\RouteServiceProvider;
use GreatMarketrealmCompanion\Providers\ViewServiceProvider;

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
    protected array $coreProviders = [
        NavigationServiceProvider::class,
        RouteServiceProvider::class,
        ViewServiceProvider::class,
    ];

    /**
     * Loaded provider instances.
     *
     * @var ServiceProvider[]
     */
    protected array $loadedProviders = [];

    /**
     * Constructor.
     *
     * @param Application $app Application instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Boot the framework.
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
    protected function register(ServiceProvider $provider): void
    {
        $this->providers[] = $provider;
    
        $provider->register();
    }

    /**
     * Register all core service providers.
     */

    protected function registerProviders(): void
    {
        foreach ($this->coreProviders as $providerClass) {
            $this->register(
                new $providerClass($this->app)
            );
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
