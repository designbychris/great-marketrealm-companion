<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Characters\ClassRegistry;
use GreatMarketrealmCompanion\Services\Characters\RaceRegistry;

defined('ABSPATH') || exit;

/**
 * Registry Service Provider.
 *
 * Registers the application's character registries.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
final class RegistryServiceProvider extends ServiceProvider
{
    /**
     * Register registry services.
     */
    public function register(): void
    {
        $container = $this->app->container();

        $container->singleton(
            RaceRegistry::class,
            static function (): RaceRegistry {
                return new RaceRegistry();
            }
        );

        $container->singleton(
            ClassRegistry::class,
            static function (): ClassRegistry {
                return new ClassRegistry();
            }
        );
    }

    /**
     * Boot registry services.
     */
    public function boot(): void
    {
        // Registries currently require no boot-time behaviour.
    }
}
