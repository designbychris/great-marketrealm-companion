<?php

namespace GreatMarketrealmCompanion\Core;

defined('ABSPATH') || exit;

/**
 * Base Service Provider.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
abstract class ServiceProvider
{
    /**
     * The application instance.
     */
    protected Application $app;

    /**
     * Constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register bindings.
     */
    public function register(): void
    {
        //
    }

    /**
     * Boot services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the application instance.
     */
    public function app(): Application
    {
        return $this->app;
    }
}
