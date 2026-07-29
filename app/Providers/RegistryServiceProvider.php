<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Characters\ClassRegistry;
use GreatMarketrealmCompanion\Services\Characters\RaceRegistry;
use GreatMarketrealmCompanion\Services\Definitions\Definitions;

defined('ABSPATH') || exit;

/**
 * Registry Service Provider.
 */
final class RegistryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->container()->singleton(
            RaceRegistry::class,
            fn (): RaceRegistry => new RaceRegistry(
                $this->app->make(Definitions::class)
            )
        );

        $this->app->container()->singleton(
            ClassRegistry::class,
            fn (): ClassRegistry => new ClassRegistry()
        );
    }

    public function boot(): void
    {
    }
}
