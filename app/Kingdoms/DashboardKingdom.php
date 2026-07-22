<?php

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\Dashboard\DashboardServiceProvider;
use GreatMarketrealmCompanion\Navigation\Icons;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

/**
 * Dashboard Kingdom.
 *
 * Describes the services, routes, and navigation belonging
 * to the Dashboard area of Marketrealm Companion.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class DashboardKingdom extends Kingdom
{
    /**
     * Return the Kingdom's unique key.
     */
    public function key(): string
    {
        return 'dashboard';
    }

    /**
     * Return the Kingdom's service provider.
     *
     * @return class-string<DashboardServiceProvider>
     */
    public function provider(): string
    {
        return DashboardServiceProvider::class;
    }

    /**
     * Return the Kingdom's route files.
     *
     * @return array<int, string>
     */
    public function routes(): array
    {
        return [
            GMRC_PATH . 'app/Modules/Dashboard/Routes.php',
        ];
    }

    /**
     * Register the Dashboard navigation item.
     */
    public function registerNavigation(
        Navigation $navigation
    ): void {
        if ($navigation->get($this->key()) !== null) {
            return;
        }

        $navigation->add(
            MenuItem::make(
                'dashboard',
                __('Dashboard'),
                Icons::DASHBOARD,
                'dashboard',
                10
            )
        );
    }
}
