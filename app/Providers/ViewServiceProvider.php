<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Core\View\ViewFinder;

defined('ABSPATH') || exit;

/**
 * View Service Provider.
 *
 * Registers the services responsible for locating
 * and rendering Guild Ledger views.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register view services.
     */
    public function register(): void
    {
        $this->app->singleton(
            ViewFinder::class
        );

        $this->app->singleton(
            ViewFactory::class
        );
    }
}
