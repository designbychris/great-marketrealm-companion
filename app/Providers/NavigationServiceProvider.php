<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Application\Navigation\Navigation;
use GreatMarketrealmCompanion\Core\Container;

defined('ABSPATH') || exit;

/**
 * Marketrealm Companion
 *
 * Navigation Service Provider
 *
 * Registers and boots the platform navigation.
 *
 * @package MarketrealmCompanion
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
                function (Container $container) {

                    return new Navigation();

                }
            );
    }

    /**
     * Boot the provider.
     */
    public function boot(): void
    {
        /** @var Navigation $navigation */
        $navigation = $this->app->make(
            Navigation::class
        );

        $navigation->registerDefaults();

        /**
         * Allow modules to extend navigation.
         */
        do_action(
            'gmrc_navigation_register',
            $navigation
        );
    }
}
