<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Application;

defined('ABSPATH') || exit;

/**
 * Base Service Provider.
 *
 * Service providers register application services and may
 * optionally perform work after every provider has registered.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
abstract class ServiceProvider
{
    /**
     * Create the service provider.
     */
    public function __construct(
        protected Application $app
    ) {
    }

    /**
     * Register services with the application.
     */
    abstract public function register(): void;

    /**
     * Boot the registered services.
     *
     * Providers only need to override this method when they
     * have work to perform after all providers are registered.
     */
    public function boot(): void
    {
    }
}
