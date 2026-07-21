<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Core\Container;

defined('ABSPATH') || exit;

/**
 * Navigation Service Provider.
 *
 * Registers and boots the platform navigation service.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.2
 */
class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app
            ->container()
            ->singleton(
                Navigation::class,
                function (Container $container): Navigation {

                    return new Navigation(
                        $container->make(PermissionManager::class),
                        $container->make(EventDispatcher::class)
                    );

                }
            );
    }

    /**
     * Boot services.
     */
    public function boot(): void
    {
        /** @var Navigation $navigation */
        $navigation = $this->app->make(
            Navigation::class
        );

        $navigation->registerDefaults();

        /**
         * Allow modules to extend the navigation.
         */
        do_action(
            'gmrc_navigation_register',
            $navigation
        );
    }
}
