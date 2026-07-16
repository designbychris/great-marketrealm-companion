<?php

namespace GreatMarketrealmCompanion\Navigation;

defined('ABSPATH') || exit;

/**
 * Navigation Registry
 *
 * Stores every registered navigation item.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.1.2
 */
class Navigation
{
    /**
     * Registered menu items.
     *
     * @var MenuItem[]
     */
    protected static array $items = [];

    /**
     * Register a menu item.
     *
     * @param MenuItem $item
     *
     * @return void
     */
    public static function register(MenuItem $item): void
    {
        self::$items[$item->getId()] = $item;
    }

    /**
     * Return all navigation items.
     *
     * @return MenuItem[]
     */
    public static function all(): array
    {
        self::sort();

        return self::$items;
    }

    /**
     * Find a navigation item.
     *
     * @param string $id
     *
     * @return MenuItem|null
     */
    public static function find(string $id): ?MenuItem
    {
        return self::$items[$id] ?? null;
    }

    /**
     * Determine whether a menu item exists.
     *
     * @param string $id
     *
     * @return bool
     */
    public static function has(string $id): bool
    {
        return isset(self::$items[$id]);
    }

    /**
     * Remove all registered items.
     *
     * Useful for testing or rebuilding navigation.
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$items = [];
    }

    /**
     * Register the platform navigation.
     *
     * @return void
     */
    public static function registerCore(): void
    {
        self::register(
            MenuItem::make('dashboard')
                ->title(__('Dashboard', 'great-marketrealm-companion'))
                ->icon('dashboard')
                ->route('dashboard')
                ->sort(10)
        );

        self::register(
            MenuItem::make('characters')
                ->title(__('Characters', 'great-marketrealm-companion'))
                ->icon('characters')
                ->route('characters')
                ->sort(20)
        );

        self::register(
            MenuItem::make('inventory')
                ->title(__('Inventory', 'great-marketrealm-companion'))
                ->icon('inventory')
                ->route('inventory')
                ->sort(30)
        );

        self::register(
            MenuItem::make('journal')
                ->title(__('Journal', 'great-marketrealm-companion'))
                ->icon('journal')
                ->route('journal')
                ->sort(40)
        );

        self::register(
            MenuItem::make('campaigns')
                ->title(__('Campaigns', 'great-marketrealm-companion'))
                ->icon('campaigns')
                ->route('campaigns')
                ->sort(50)
        );

        self::register(
            MenuItem::make('quests')
                ->title(__('Quests', 'great-marketrealm-companion'))
                ->icon('quests')
                ->route('quests')
                ->sort(60)
        );

        self::register(
            MenuItem::make('compendium')
                ->title(__('Compendium', 'great-marketrealm-companion'))
                ->icon('compendium')
                ->route('compendium')
                ->sort(70)
        );

        self::register(
            MenuItem::make('dice')
                ->title(__('Dice Roller', 'great-marketrealm-companion'))
                ->icon('dice')
                ->route('dice')
                ->sort(80)
        );

        /**
         * Allow modules to register navigation items.
         */
        do_action('gmrc_navigation_register');
    }

    /**
     * Sort menu items.
     *
     * @return void
     */
    protected static function sort(): void
    {
        uasort(
            self::$items,
            static fn (MenuItem $a, MenuItem $b)
                => $a->getSort() <=> $b->getSort()
        );
    }
}
