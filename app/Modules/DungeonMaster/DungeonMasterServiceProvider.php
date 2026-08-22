<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers\DungeonMasterController;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;
use GreatMarketrealmCompanion\Providers\ServiceProvider;

defined('ABSPATH') || exit;

final class DungeonMasterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DungeonMasterAccess::class);

        $this->app->bind(
            DungeonMasterController::class,
            static fn (Container $container): DungeonMasterController =>
                new DungeonMasterController(
                    $container->make(ViewFactory::class),
                    $container->make(DungeonMasterAccess::class)
                )
        );
    }
}
