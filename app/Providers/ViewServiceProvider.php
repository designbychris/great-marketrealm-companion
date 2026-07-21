<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Core\View\ViewFinder;

defined('ABSPATH') || exit;

/**
 * View Service Provider.
 *
 * Registers view-related services.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->container()->singleton(
            ViewFinder::class,
            fn () => new ViewFinder()
        );

        $this->app->container()->singleton(
            ViewFactory::class,
            fn () => new ViewFactory(
                $this->app->container()->make(
                    ViewFinder::class
                )
            )
        );
    }

    /**
     * Boot the provider.
     */
    public function boot(): void
    {
        // Nothing to boot yet.
    }
}
