<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Permissions\PermissionManager;

defined('ABSPATH') || exit;

class NavigationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $container = $this->app->container();

        $container->singleton(
            PermissionManager::class,
            static function (): PermissionManager {
                return new PermissionManager();
            }
        );

        $container->singleton(
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
