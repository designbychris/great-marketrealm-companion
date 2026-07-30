<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Modules\ModuleRegistry;

defined('ABSPATH') || exit;

/**
 * Module Service Provider.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ModuleRegistry::class
        );
    }
}
