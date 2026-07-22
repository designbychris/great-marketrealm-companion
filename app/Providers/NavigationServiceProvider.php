<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Permissions\PermissionManager;

defined('ABSPATH') || exit;

/**
 * Navigation Service Provider.
 *
 * Registers and configures application navigation.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
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
            static function (): Navigation {
                return new Navigation();
            }
        );
    }

    public function boot(): void
    {
        $navigation = $this->app->make(
            Navigation::class
        );

        $navigation->registerDefaults();
    }
}
