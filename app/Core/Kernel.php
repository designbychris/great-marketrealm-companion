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
     * Registered service providers.
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
            $provider->register();
        }

        foreach ($this->providers as $provider) {
            $provider->boot();
        }

        /**
         * Fires once the Great Marketrealm Companion
         * has finished booting.
         */
        do_action('gmrc_booted', $this->app);
    }

    /**
     * Register a service provider.
     */
    public function register(ServiceProvider $provider): self
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * Register all core service providers.
     */
    protected function registerProviders(): void
    {
        // These will be added over the next few milestones.
        //
        // $this->register(new NavigationServiceProvider($this->app));
        // $this->register(new ViewServiceProvider($this->app));
        // $this->register(new ModuleServiceProvider($this->app));
        // $this->register(new RouteServiceProvider($this->app));
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
