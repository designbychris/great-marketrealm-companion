<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

/**
 * Navigation Service Provider.
 *
 * Registers the application's shared navigation collection.
 *
 * Navigation items are contributed by Kingdoms and, in the
 * future, Guild Halls rather than being hard-coded here.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register navigation services.
     */
    public function register(): void
    {
        $this->app->singleton(
            Navigation::class
        );
    }
}
