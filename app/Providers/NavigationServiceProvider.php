<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

/**
 * Navigation Service Provider.
 *
 * Registers the application's navigation service.
 *
 * Navigation items are contributed by individual Kingdoms rather
 * than being hard-coded inside the Navigation service.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register navigation services.
     */
    public function register(): void
    {
        $this->app->container()->singleton(
            Navigation::class,
            static fn (): Navigation => new Navigation()
        );
    }

    /**
     * Boot navigation services.
     */
    public function boot(): void
    {
        // Kingdoms register their navigation separately.
    }
}
