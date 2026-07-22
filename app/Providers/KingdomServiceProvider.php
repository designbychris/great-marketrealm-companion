<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Kingdoms\CharactersKingdom;
use GreatMarketrealmCompanion\Kingdoms\DashboardKingdom;
use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;

defined('ABSPATH') || exit;

/**
 * Kingdom Service Provider.
 *
 * Registers the Kingdom registry and the Kingdoms installed
 * within Marketrealm Companion.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class KingdomServiceProvider extends ServiceProvider
{
    /**
     * Register Kingdom services.
     */
    public function register(): void
    {
        $this->app->container()->singleton(
            KingdomRegistry::class,
            function (): KingdomRegistry {
                $registry = new KingdomRegistry();

                $registry->add(
                    new DashboardKingdom($this->app)
                );

                $registry->add(
                    new CharactersKingdom($this->app)
                );

                return $registry;
            }
        );
    }

    /**
     * Boot Kingdom services.
     */
    public function boot(): void
    {
        /*
         * Kingdom providers, routes, and navigation will be
         * connected in the next stages.
         */
    }
}
