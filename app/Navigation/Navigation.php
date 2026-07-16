<?php

namespace GreatMarketrealmCompanion\Navigation;

defined('ABSPATH') || exit;

/**
 * Navigation Registry
 *
 * Registers and returns navigation items.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.1.2
 */
class Navigation
{
    /**
     * Registered navigation items.
     *
     * @var MenuItem[]
     */
    protected static array $items = [];

    /**
     * Boot the navigation.
     *
     * Registers the core navigation items.
     *
     * @return void
     */
    public static function boot(): void
    {
        if (! empty(self::$items)) {
            return;
        }

        self::registerCoreItems();

        /**
         * Allow modules to register navigation.
         */
        do_action('gmrc_navigation_register');
    }

    /**
     * Register a menu item.
     *
     * @param MenuItem $item
     *
     * @return void
     */
    public static function add(MenuItem $item): void
    {
        self::$items[$item->getId()] = $item;
    }

    /**
     * Return every navigation item.
     *
     * @return MenuItem[]
     */
    public static function all(): array
    {
        self::boot();

        uasort(
            self::$items,
            fn (MenuItem $a, MenuItem $b)
                => $a->getSort() <=> $b->getSort()
        );

        return self::$items;
    }

    /**
     * Register the core application navigation.
     *
     * @return void
     */
    protected static function registerCoreItems(): void
    {
        self::add(
            MenuItem::make('dashboard')
                ->title(__('Dashboard', 'great-marketrealm-companion'))
                ->icon('dashboard')
                ->route('dashboard')
                ->sort(10)
        );

        self::add(
            MenuItem::make('characters')
                ->title(__('Characters', 'great-marketrealm-companion'))
                ->icon('characters')
                ->route('characters')
                ->sort(20)
        );

        self::add(
            MenuItem::make('inventory')
                ->title(__('Inventory', 'great-marketrealm-companion'))
                ->icon('inventory')
                ->route('inventory')
                ->sort(30)
        );

        self::add(
            MenuItem::make('journal')
                ->title(__('Journal', 'great-marketrealm-companion'))
                ->icon('journal')
                ->route('journal')
                ->sort(40)
        );

        self::add(
            MenuItem::make('campaigns')
                ->title(__('Campaigns', 'great-marketrealm-companion'))
                ->icon('campaigns')
                ->route('campaigns')
                ->sort(50)
        );

        self::add(
            MenuItem::make('quests')
                ->title(__('Quests', 'great-marketrealm-companion'))
                ->icon('quests')
                ->route('quests')
                ->sort(60)
        );

        self::add(
            MenuItem::make('compendium')
                ->title(__('Compendium', 'great-marketrealm-companion'))
                ->icon('compendium')
                ->route('compendium')
                ->sort(70)
        );

        self::add(
            MenuItem::make('dice')
                ->title(__('Dice Roller', 'great-marketrealm-companion'))
                ->icon('dice')
                ->route('dice')
                ->sort(80)
        );
    }
}
