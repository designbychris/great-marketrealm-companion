<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Characters\ClassRegistry;
use GreatMarketrealmCompanion\Services\Characters\RaceRegistry;
use GreatMarketrealmCompanion\Services\Definitions\Definitions;

defined('ABSPATH') || exit;

/**
 * Registry Service Provider.
 *
 * Registers services belonging to the Registry domain.
 *
 * @since 0.3.0
 */
final class RegistryServiceProvider extends ServiceProvider
{
    /**
     * Register Registry services.
     */
    public function register(): void
    {
        $this->app->singleton(
            RaceRegistry::class,
            fn (): RaceRegistry => new RaceRegistry(
                $this->app->definitions()
            )
        );

        $this->app->singleton(
            ClassRegistry::class,
            fn (): ClassRegistry => new ClassRegistry(
                $this->app->definitions()
            )
        );
    }

    /**
     * Boot Registry services.
     */
    public function boot(): void
    {
    }
}
