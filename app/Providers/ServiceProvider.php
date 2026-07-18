<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Application;

defined('ABSPATH') || exit;

/**
 * Base Service Provider.
 *
 * All framework service providers extend this class.
 *
 * @package MarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
abstract class ServiceProvider
{
    /**
     * Application instance.
     */
    protected Application $app;

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
     * Register services.
     */
    abstract public function register(): void;

    /**
     * Boot services.
     */
    abstract public function boot(): void;
}
