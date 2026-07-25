<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Session\FlashStore;
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
        $this->app->singleton(
            ViewFinder::class,
            function (): ViewFinder {
                return new ViewFinder();
            }
        );

        $this->app->singleton(
            ViewFactory::class,
            function ($app): ViewFactory {
                return new ViewFactory(
                    $app->make(ViewFinder::class),
                    $app->make(FlashStore::class)
                );
            }
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
