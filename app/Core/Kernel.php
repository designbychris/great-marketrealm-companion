<?php

namespace GreatMarketrealmCompanion\Core;

use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;
use GreatMarketrealmCompanion\Providers\FrontendServiceProvider;
use GreatMarketrealmCompanion\Providers\KingdomServiceProvider;
use GreatMarketrealmCompanion\Providers\NavigationServiceProvider;
use GreatMarketrealmCompanion\Providers\RouteServiceProvider;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
use GreatMarketrealmCompanion\Providers\ViewServiceProvider;

defined('ABSPATH') || exit;

/**
 * Framework Kernel.
 *
 * Coordinates the platform boot process.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
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
        KingdomServiceProvider::class,
    
        NavigationServiceProvider::class,
        ViewServiceProvider::class,
    
        DashboardServiceProvider::class,
        CharactersServiceProvider::class,
    
        RouteServiceProvider::class,
        FrontendServiceProvider::class,
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

        foreach ($this->loadedProviders as $provider) {
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
        $this->loadedProviders[] = $provider;

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
     * Return the loaded providers.
     *
     * @return ServiceProvider[]
     */
    public function providers(): array
    {
        return $this->loadedProviders;
    }
}
