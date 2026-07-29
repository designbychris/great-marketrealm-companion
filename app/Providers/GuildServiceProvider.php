<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Guild\GuildSealRegistry;

class GuildServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->container()->bind(
            GuildSealRegistry::class,
            static function (): GuildSealRegistry {
                return new GuildSealRegistry();
            }
        );
    }

    public function boot(): void
    {
    }
}
