<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Permissions\PermissionManager;

defined('ABSPATH') || exit;

class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register navigation services.
     */
    public function register(): void
    {
        $this->app->singleton(
            PermissionManager::class,
            static function (): PermissionManager {
                return new PermissionManager();
            }
        );

        $this->app->singleton(
            Navigation::class,
            function (): Navigation {
                return new Navigation(
                    $this->app->make(PermissionManager::class)
                );
            }
        );
    }

    /**
     * Boot navigation services.
     */
    public function boot(): void
    {
        $navigation = $this->app->make(Navigation::class);

        // Register navigation items here.
    }
}
