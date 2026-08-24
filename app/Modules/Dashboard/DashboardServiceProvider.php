<?php

namespace GreatMarketrealmCompanion\Modules\Dashboard;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Dashboard\Controllers\DashboardController;
use GreatMarketrealmCompanion\Modules\Dashboard\Services\GuildHallDirectory;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
use GreatMarketrealmCompanion\Services\Codex\Codex;

defined('ABSPATH') || exit;

class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GuildHallDirectory::class);

        $this->app->container()->bind(
            DashboardController::class,
            static fn (Container $container): DashboardController => new DashboardController(
                $container->make(ViewFactory::class),
                $container->make(Codex::class),
                $container->make(GuildHallDirectory::class)
            )
        );
    }

    public function boot(): void
    {
    }
}
