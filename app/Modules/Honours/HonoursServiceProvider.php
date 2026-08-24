<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Honours;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildMembershipSummary;
use GreatMarketrealmCompanion\Modules\Honours\Controllers\HonoursController;
use GreatMarketrealmCompanion\Modules\Honours\Services\BookOfDeeds;
use GreatMarketrealmCompanion\Modules\Honours\Services\GuildHonourLedger;
use GreatMarketrealmCompanion\Modules\Honours\Services\GuildHonourRegistry;
use GreatMarketrealmCompanion\Providers\ServiceProvider;

defined('ABSPATH') || exit;

final class HonoursServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GuildHonourRegistry::class);
        $this->app->singleton(GuildHonourLedger::class);
        $this->app->container()->bind(
            BookOfDeeds::class,
            static fn (Container $container): BookOfDeeds => new BookOfDeeds(
                $container->make(GuildMembershipSummary::class),
                $container->make(GuildHonourRegistry::class),
                $container->make(GuildHonourLedger::class)
            )
        );
        $this->app->container()->bind(
            HonoursController::class,
            static fn (Container $container): HonoursController => new HonoursController(
                $container->make(ViewFactory::class),
                $container->make(BookOfDeeds::class)
            )
        );
    }
}
