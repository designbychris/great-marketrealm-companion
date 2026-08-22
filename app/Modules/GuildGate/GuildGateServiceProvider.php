<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Modules\GuildGate\Controllers\GuildGateController;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\AuthenticateGuildMember;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildPortraitManager;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildRoleRegistrar;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\RegisterGuildMember;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\UpdateGuildProfile;
use GreatMarketrealmCompanion\Providers\ServiceProvider;

defined('ABSPATH') || exit;

final class GuildGateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GuildRoleRegistrar::class);
        $this->app->singleton(AuthenticateGuildMember::class);
        $this->app->singleton(RegisterGuildMember::class);
        $this->app->singleton(UpdateGuildProfile::class);
        $this->app->singleton(GuildPortraitManager::class);
        $this->app->bind(
            GuildGateController::class,
            static fn (Container $container): GuildGateController =>
                new GuildGateController(
                    $container->make(\GreatMarketrealmCompanion\Core\View\ViewFactory::class),
                    $container->make(\GreatMarketrealmCompanion\Core\Http\Request::class),
                    $container->make(\GreatMarketrealmCompanion\Core\Routing\Router::class),
                    $container->make(\GreatMarketrealmCompanion\Core\Http\ResponseFactory::class),
                    $container->make(\GreatMarketrealmCompanion\Core\Session\FlashStore::class),
                    $container->make(AuthenticateGuildMember::class),
                    $container->make(RegisterGuildMember::class),
                    $container->make(UpdateGuildProfile::class),
                    $container->make(GuildPortraitManager::class)
                )
        );
    }

    public function boot(): void
    {
        $this->app->make(GuildRoleRegistrar::class)->register();
    }
}
