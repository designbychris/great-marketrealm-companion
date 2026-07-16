<?php

namespace GreatMarketrealmCompanion\Navigation;

defined('ABSPATH') || exit;

/**
 * Navigation Registry
 *
 * Stores every registered navigation item for the application.
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
     * Register a navigation item.
     */
    public static function register(MenuItem $item): void
    {
        self::$items[$item->getId()] = $item;
    }

    /**
     * Return every registered item.
     *
     * @return MenuItem[]
     */
    public static function all(): array
    {
        return self::sorted(self::$items);
    }

    /**
     * Find an item by ID.
     */
    public static function find(string $id): ?MenuItem
    {
        return self::$items[$id] ?? null;
    }

    /**
     * Determine if an item exists.
     */
    public static function has(string $id): bool
    {
        return isset(self::$items[$id]);
    }

    /**
     * Remove every registered item.
     *
     * Useful during bootstrapping and testing.
     */
    public static function clear(): void
    {
        self::$items = [];
    }

    /**
     * Return the number of registered items.
     */
    public static function count(): int
    {
        return count(self::$items);
    }

    /**
     * Sort menu items by sort order.
     *
     * @param MenuItem[] $items
     *
     * @return MenuItem[]
     */
    protected static function sorted(array $items): array
    {
        uasort(
            $items,
            static fn (MenuItem $a, MenuItem $b)
                => $a->getSort() <=> $b->getSort()
        );

        return $items;
    }
}
