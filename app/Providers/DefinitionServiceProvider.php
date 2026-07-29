<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Definitions\Definitions;

defined('ABSPATH') || exit;

/**
 * Definition Service Provider.
 *
 * Registers services belonging to the Definition domain.
 *
 * @since 0.3.0
 */
final class DefinitionServiceProvider extends ServiceProvider
{
    /**
     * Register Definition services.
     */
    public function register(): void
    {
        $this->app->singleton(
            Definitions::class,
            static fn (): Definitions => new Definitions()
        );
    }

    /**
     * Boot Definition services.
     */
    public function boot(): void
    {
    }
}
