<?php

namespace GreatMarketrealmCompanion\Navigation;

defined('ABSPATH') || exit;

/**
 * Navigation collection.
 *
 * Stores navigation items registered by installed Kingdoms.
 *
 * The Navigation service does not know which Kingdoms exist.
 * Each Kingdom is responsible for contributing its own items.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class Navigation
{
    /**
     * Registered navigation items.
     *
     * @var array<string, MenuItem>
     */
    protected array $items = [];

    /**
     * Add a navigation item.
     */
    public function add(MenuItem $item): void
    {
        $this->items[$item->key()] = $item;
    }

    /**
     * Determine whether an item has been registered.
     */
    public function has(string $key): bool
    {
        return isset(
            $this->items[$key]
        );
    }

    /**
     * Return a navigation item by key.
     */
    public function get(string $key): ?MenuItem
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Remove a navigation item.
     */
    public function remove(string $key): void
    {
        unset(
            $this->items[$key]
        );
    }

    /**
     * Return all registered navigation items.
     *
     * @return array<string, MenuItem>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Remove all navigation items.
     */
    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * Return the number of registered navigation items.
     */
    public function count(): int
    {
        return count($this->items);
    }
}
