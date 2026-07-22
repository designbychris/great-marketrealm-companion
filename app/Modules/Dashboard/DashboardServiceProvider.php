<?php

namespace GreatMarketrealmCompanion\Modules\Dashboard;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Dashboard\Controllers\DashboardController;
use GreatMarketrealmCompanion\Providers\ServiceProvider;

defined('ABSPATH') || exit;

/**
 * Dashboard Service Provider.
 *
 * Registers services belonging to the Dashboard Kingdom.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->container()->bind(
            DashboardController::class,
            static function (Container $container): DashboardController {
                return new DashboardController(
                    $container->make(ViewFactory::class)
                );
            }
        );
    }

    public function boot(): void
    {
        // Dashboard boot logic will live here later.
    }
}
