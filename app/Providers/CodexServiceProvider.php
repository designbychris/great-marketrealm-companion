<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Characters\ClassRegistry;
use GreatMarketrealmCompanion\Services\Characters\RaceRegistry;
use GreatMarketrealmCompanion\Services\Codex\Codex;

defined('ABSPATH') || exit;

/**
 * Codex Service Provider.
 *
 * Registers the Codex and its core knowledge contributors.
 *
 * @since 0.3.0
 */
final class CodexServiceProvider extends ServiceProvider
{
    /**
     * Register Codex services.
     */
    public function register(): void
    {
        $this->app->singleton(
            Codex::class,
            fn (): Codex => new Codex()
        );
    }

    /**
     * Boot the Codex.
     */
    public function boot(): void
    {
        $codex = $this->app->make(Codex::class);

        $codex
            ->register(
                section: 'races',
                name: 'Races',
                registry: $this->app->make(RaceRegistry::class)
            )
            ->register(
                section: 'classes',
                name: 'Classes',
                registry: $this->app->make(ClassRegistry::class)
            );

        /**
         * Fires after the core Codex sections have been registered.
         *
         * Expansion packs and third-party integrations can contribute
         * additional registries during this hook.
         */
        do_action(
            'gmrc_codex_register',
            $codex,
            $this->app
        );
    }
}
