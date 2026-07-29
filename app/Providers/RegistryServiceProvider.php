<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Characters\ClassRegistry;
use GreatMarketrealmCompanion\Services\Characters\RaceRegistry;

final class RegistryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $container = $this->app->container();

        $container->singleton(
            RaceRegistry::class,
            static fn () => new RaceRegistry()
        );

        $container->singleton(
            ClassRegistry::class,
            static fn () => new ClassRegistry()
        );
    }

    public function boot(): void
    {
        // Nothing to boot yet.
    }
}
