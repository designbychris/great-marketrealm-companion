<?php

namespace GreatMarketrealmCompanion\Core;

use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;
use GreatMarketrealmCompanion\Providers\FrontendServiceProvider;
use GreatMarketrealmCompanion\Providers\KingdomServiceProvider;
use GreatMarketrealmCompanion\Providers\NavigationServiceProvider;
use GreatMarketrealmCompanion\Providers\ResourceServiceProvider;
use GreatMarketrealmCompanion\Providers\PageServiceProvider;
use GreatMarketrealmCompanion\Providers\RouteServiceProvider;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
use GreatMarketrealmCompanion\Providers\ViewServiceProvider;

use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Application Kernel.
 *
 * Coordinates registration and booting of framework
 * and Kingdom service providers.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class Kernel
{
    /**
     * Registered provider instances.
     *
     * @var array<int, ServiceProvider>
     */
    protected array $providers = [];

    /**
     * Providers required before Kingdom registration.
     *
     * @var array<int, class-string<ServiceProvider>>
     */
    protected array $foundationProviders = [
        KingdomServiceProvider::class,
        NavigationServiceProvider::class,
        ViewServiceProvider::class,
    ];

    /**
     * Providers registered after Kingdom providers.
     *
     * @var array<int, class-string<ServiceProvider>>
     */
    protected array $applicationProviders = [
        ResourceServiceProvider::class,
        PageServiceProvider::class,
        RouteServiceProvider::class,
        FrontendServiceProvider::class,
    ];

    /**
     * Create the application Kernel.
     */
    public function __construct(
        protected Application $app
    ) {
    }

    /**
     * Boot the application.
     */
    public function boot(): void
    {
        $this->registerProviders(
            $this->foundationProviders
        );

        $this->registerKingdomProviders();

        $this->registerProviders(
            $this->applicationProviders
        );

        $this->bootProviders();
    }

    /**
     * Register a collection of providers.
     *
     * @param array<int, class-string<ServiceProvider>> $providers
     */
    protected function registerProviders(
        array $providers
    ): void {
        foreach ($providers as $providerClass) {
            $this->registerProvider(
                $providerClass
            );
        }
    }

    /**
     * Register providers contributed by Kingdoms.
     */
    protected function registerKingdomProviders(): void
    {
        $registry = $this->app->make(
            KingdomRegistry::class
        );

        foreach ($registry->providers() as $providerClass) {
            $this->registerProvider(
                $providerClass
            );
        }
    }

    /**
     * Register one service provider.
     *
     * @param class-string<ServiceProvider> $providerClass
     */
    protected function registerProvider(
    string $providerClass
    ): void {
        if (isset(
            $this->registeredProviderClasses[$providerClass]
        )) {
            return;
        }
    
        if (! is_subclass_of(
            $providerClass,
            ServiceProvider::class
        )) {
            throw new RuntimeException(
                sprintf(
                    'Invalid service provider: %s',
                    $providerClass
                )
            );
        }
    
        $provider = new $providerClass(
            $this->app
        );
    
        $provider->register();
    
        $this->providers[] = $provider;
    
        $this->registeredProviderClasses[
            $providerClass
        ] = true;
    }

    /**
     * Boot every registered provider.
     */
    protected function bootProviders(): void
    {
        foreach ($this->providers as $provider) {
            error_log(
                'GMRC booting provider: ' .
                get_class($provider)
            );
    
            $provider->boot();
        }
    }

    /**
     * Registered provider classes.
     *
     * @var array<class-string<ServiceProvider>, bool>
     */
    protected array $registeredProviderClasses = [];
    }
