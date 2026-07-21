<?php

namespace GreatMarketrealmCompanion\Permissions;

defined('ABSPATH') || exit;

class PermissionManager
{
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

    public function boot(): void
    {
        $navigation = $this->app->make(Navigation::class);

        // Register navigation items here.
    }
}
